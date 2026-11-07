<?php

namespace App\Http\Controllers;

use App\Jobs\SendEnquiryNotification;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EnquiryController
{
    public function store(Request $r, string $locale)
    {
        $r->validate(['version_id' => 'required|integer', 'idempotency_key' => 'required|uuid', 'website' => 'prohibited', 'answers' => 'required|array']);
        $retry = fn () => DB::table('enquiries')->where(['idempotency_key' => $r->idempotency_key, 'locale' => $locale, 'contact_email' => $r->input('answers.contact_email')])->value('public_reference');
        $done = fn ($ref) => redirect("/$locale#kontakt")->with('reference', $ref);
        if ($reference = $retry()) {
            return $done($reference);
        }
        $version = DB::table('form_versions')->where('id', $r->version_id)->where(fn ($q) => $q->where('status', 'published')->orWhere(fn ($q) => $q->where('status', 'retired')->where('retired_at', '>=', now()->subDay())))->first();
        if (! $version) {
            throw ValidationException::withMessages(['version_id' => __('forms.expired')]);
        }
        $fields = collect(json_decode($version->schema, true))->where('active', true)->keyBy('key');
        $rules = ['answers' => ['required', 'array:'.$fields->keys()->implode(',')]];
        $labels = [];
        $snapshot = [];
        foreach ($fields as $key => $f) {
            $path = "answers.$key";
            $type = $f['type'];
            $rules[$path] = [$f['required'] ? 'required' : 'nullable'];
            $options = array_column($f['options'] ?? [], 'value');
            $rule = match ($type) {
                'select','radio' => ['string', Rule::in($options)],
                'multi' => ['array', 'max:50'], 'checkbox' => [$f['required'] ? 'accepted' : 'boolean'],
                'email' => ['email', 'max:254'], 'date' => ['date_format:Y-m-d'],
                'file' => ['array', 'max:3'], 'textarea' => ['string', 'max:8000'], default => ['string', 'max:255']
            };
            array_push($rules[$path], ...$rule);
            if (isset($f['max_length'])) {
                $rules[$path][] = 'max:'.$f['max_length'];
            }
            if ($type === 'multi') {
                $rules[$path.'.*'] = ['string', 'distinct', Rule::in($options)];
            }
            if ($type === 'file') {
                $rules[$path.'.*'] = ['file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,webp', 'extensions:pdf,jpg,jpeg,png,webp', function ($attribute, $file, $fail) {
                    if (! $file instanceof UploadedFile || ! $file->isValid()) {
                        return;
                    }
                    $types = ['pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
                    if (($types[strtolower($file->getClientOriginalExtension())] ?? null) !== $file->getMimeType()) {
                        $fail(__('forms.invalid'));
                    }
                }];
            }
            $labels[$path] = $f['labels'][$locale];
            $snapshot[$key] = ['label' => $labels[$path], 'type' => $type, 'options' => collect($f['options'] ?? [])->mapWithKeys(fn ($o) => [$o['value'] => $o['labels'][$locale]])];
        }
        $validator = Validator::make($r->all(), $rules, ['required' => __('forms.required'), 'answers.array' => __('forms.invalid')], $labels);
        $validator->after(function ($v) use ($r) {
            if (array_diff(array_keys($r->all()), ['_token', 'version_id', 'idempotency_key', 'website', 'answers', 'cf-turnstile-response'])) {
                $v->errors()->add('form', __('forms.invalid'));
            }
            if (count(Arr::flatten($r->allFiles())) > 3) {
                $v->errors()->add('form', __('forms.invalid'));
            }
        });
        $answers = $validator->validate()['answers'];
        if (config('studio.turnstile_secret') || config('studio.turnstile_key')) {
            $ok = config('studio.turnstile_secret') && config('studio.turnstile_key') && rescue(fn () => Http::timeout(5)->asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', ['secret' => config('studio.turnstile_secret'), 'response' => $r->input('cf-turnstile-response'), 'remoteip' => $r->ip()])->json('success'), false);
            if (! $ok) {
                throw ValidationException::withMessages(['form' => __('forms.invalid')]);
            }
        }
        $paths = [];
        $reference = Str::upper(Str::random(16));
        try {
            DB::transaction(function () use ($r, $locale, $version, $snapshot, $answers, &$paths, $reference) {
                $id = DB::table('enquiries')->insertGetId(['public_reference' => $reference, 'idempotency_key' => $r->idempotency_key, 'form_version_id' => $version->id, 'locale' => $locale, 'contact_name' => $answers['contact_name'], 'contact_email' => $answers['contact_email'], 'company' => $answers['company'] ?? null, 'service_code' => $answers['service'] ?? null, 'budget_code' => $answers['budget'] ?? null, 'schema_snapshot' => json_encode($snapshot, JSON_THROW_ON_ERROR), 'submitted_at' => now()]);
                foreach ($answers as $key => $value) {
                    if ($snapshot[$key]['type'] === 'file') {
                        $names = [];
                        foreach ($value ?? [] as $file) {
                            $path = $file->store('enquiries', 'local');
                            if (! $path) {
                                throw new \RuntimeException('Private storage write failed');
                            }
                            $paths[] = $path;
                            $name = Str::limit(basename($file->getClientOriginalName()), 200, '');
                            DB::table('enquiry_attachments')->insert(['enquiry_id' => $id, 'field_key' => $key, 'path' => $path, 'original_name' => $name, 'mime' => $file->getMimeType(), 'size' => $file->getSize()]);
                            $names[] = $name;
                        }
                        $value = $names;
                    }
                    DB::table('enquiry_answers')->insert(['enquiry_id' => $id, 'field_key' => $key, 'answer' => json_encode($value, JSON_THROW_ON_ERROR)]);
                }
                DB::table('enquiry_events')->insert(['enquiry_id' => $id, 'new_status' => 'new', 'created_at' => now()]);
                // The database queue on the same connection is an atomic outbox:
                // workers see the job only after this transaction commits.
                SendEnquiryNotification::dispatch($reference)->onConnection('database')->beforeCommit();
            });
        } catch (\Throwable $e) {
            foreach ($paths as $path) {
                Storage::disk('local')->delete($path);
            }
            if ($e instanceof UniqueConstraintViolationException && $existing = $retry()) {
                return $done($existing);
            }
            if ($e instanceof UniqueConstraintViolationException) {
                abort(409);
            }
            throw $e;
        }

        return $done($reference);
    }
}
