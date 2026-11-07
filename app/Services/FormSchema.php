<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FormSchema
{
    public const TYPES = ['text', 'textarea', 'email', 'tel', 'select', 'radio', 'multi', 'date', 'checkbox', 'file'];

    public const SERVICES = ['ai', 'software', 'apps', 'web', 'commerce', 'games'];

    private const CORE = ['contact_name' => 'text', 'contact_email' => 'email', 'message' => 'textarea'];

    public function defaults(): array
    {
        $fields = [
            ['contact_name', 'text', 'Name', 'Name', 'Ad soyad', true],
            ['contact_email', 'email', 'E-Mail', 'Email', 'E-posta', true],
            ['company', 'text', 'Unternehmen', 'Company', 'Şirket', false],
            ['phone', 'tel', 'Telefon', 'Phone', 'Telefon', false],
            ['service', 'select', 'Leistung', 'Service', 'Hizmet', true],
            ['budget', 'select', 'Budget', 'Budget', 'Bütçe', false],
            ['start', 'date', 'Gewünschter Start', 'Preferred start', 'Tahmini başlangıç', false],
            ['message', 'textarea', 'Ihr Projekt', 'Your project', 'Projeniz', true],
            ['attachments', 'file', 'Anhänge', 'Attachments', 'Dosya ekleri', false],
        ];
        $services = [
            ['KI & Automatisierung', 'AI & automation', 'Yapay zekâ ve otomasyon'],
            ['Individualsoftware', 'Custom software', 'Özel yazılım'],
            ['Mobile Apps', 'Mobile apps', 'Mobil uygulamalar'],
            ['Webentwicklung', 'Web development', 'Web geliştirme'],
            ['E-Commerce', 'E-commerce', 'E-ticaret'],
            ['Spiele', 'Games', 'Oyun'],
        ];
        $schema = [];
        foreach ($fields as [$key,$type,$de,$en,$tr,$required]) {
            $options = [];
            if ($key === 'service') {
                foreach (self::SERVICES as $i => $value) {
                    $options[] = ['value' => $value, 'labels' => array_combine(['de', 'en', 'tr'], $services[$i])];
                }
            }
            if ($key === 'budget') {
                foreach (['unknown', '0-5k', '5-15k', '15-30k', '30k+'] as $i => $value) {
                    $range = ['', '0–5.000 €', '5.000–15.000 €', '15.000–30.000 €', '30.000 € +'][$i];
                    $options[] = ['value' => $value, 'labels' => array_combine(['de', 'en', 'tr'], $i ? array_fill(0, 3, $range) : ['Noch offen', 'Undecided', 'Henüz belli değil'])];
                }
            }
            $schema[] = compact('key', 'type', 'required', 'options') + ['active' => true, 'width' => in_array($type, ['file', 'textarea']) ? 'full' : 'half', 'labels' => compact('de', 'en', 'tr')];
        }

        return $schema;
    }

    public function check(array $fields, array $previous = []): void
    {
        $error = fn ($message) => throw ValidationException::withMessages(['schema' => $message]);
        $locales = DB::table('locales')->where('is_active', true)->pluck('code');
        if (count($fields) > 50 || ! array_is_list($fields)) {
            $error('En fazla 50 sıralı alan kullanılabilir.');
        }
        $known = array_column($previous, 'type', 'key');
        $keys = [];
        foreach ($fields as $f) {
            if (! is_array($f) || array_diff(array_keys($f), ['key', 'type', 'active', 'required', 'width', 'labels', 'options', 'placeholder', 'help', 'max_length'])) {
                $error('Desteklenmeyen alan ayarı.');
            }
            $key = $f['key'] ?? '';
            $type = $f['type'] ?? '';
            if (! is_string($key) || ! preg_match('/^[a-z][a-z0-9_]{0,49}$/D', $key) || isset($keys[$key]) || ! in_array($type, self::TYPES, true)) {
                $error('Alan anahtarı veya türü geçersiz.');
            }
            if (isset($known[$key]) && $known[$key] !== $type) {
                $error('Mevcut anahtarın türü değiştirilemez.');
            }
            if (! is_bool($f['active'] ?? null) || ! is_bool($f['required'] ?? null) || ! in_array($f['width'] ?? '', ['full', 'half'], true)) {
                $error('Alan ayarlarını kontrol edin.');
            }
            if (isset($f['max_length']) && (! in_array($type, ['text', 'textarea', 'tel', 'email'], true) || ! is_int($f['max_length']) || $f['max_length'] < 1 || $f['max_length'] > ($type === 'textarea' ? 8000 : 254))) {
                $error('Karakter sınırı geçersiz.');
            }
            foreach (['labels', 'placeholder', 'help'] as $textGroup) {
                if (isset($f[$textGroup]) && ! is_array($f[$textGroup])) {
                    $error('Dil metinleri nesne olmalıdır.');
                }
                foreach ($f[$textGroup] ?? [] as $text) {
                    if (! is_string($text) || mb_strlen($text) > 2000) {
                        $error('Alan metni en fazla 2000 karakter olabilir.');
                    }
                }
            }
            if (! is_array($f['options'] ?? []) || ! array_is_list($f['options'] ?? []) || count($f['options'] ?? []) > 100) {
                $error('Seçenek listesi geçersiz.');
            }
            $keys[$key] = $type;
            if (isset(self::CORE[$key]) && (! $f['active'] || ! $f['required'] || self::CORE[$key] !== $type)) {
                $error("Korunan alan: $key");
            }
            foreach ($locales as $l) {
                if (! is_string($f['labels'][$l] ?? null) || trim($f['labels'][$l]) === '') {
                    $error("$key için $l etiketi eksik.");
                }
            }
            $values = [];
            foreach ($f['options'] ?? [] as $opt) {
                if (! is_array($opt) || array_diff(array_keys($opt), ['value', 'labels']) || ! is_array($opt['labels'] ?? null)) {
                    $error('Seçenek ayarı geçersiz.');
                }
                $v = $opt['value'] ?? null;
                if (! is_string($v) || ! preg_match('/^[a-z0-9_+\-]{1,50}$/D', $v) || in_array($v, $values, true)) {
                    $error('Seçenek değeri geçersiz.');
                }
                foreach ($locales as $l) {
                    if (! is_string($opt['labels'][$l] ?? null) || trim($opt['labels'][$l]) === '') {
                        $error("$key seçeneği için $l eksik.");
                    }
                }
                $values[] = $v;
            }
            if (in_array($type, ['select', 'radio', 'multi']) && ! $values) {
                $error("$key için seçenek gerekli.");
            }
            if ($key === 'service' && ($type !== 'select' || array_diff($values, self::SERVICES))) {
                $error('Hizmet kodu geçersiz.');
            }
        }
        if (array_diff_key(self::CORE, $keys)) {
            $error('Ad, e-posta ve proje açıklaması korunmalıdır.');
        }
    }

    public function active()
    {
        return DB::table('form_versions')->join('forms', 'forms.active_version_id', '=', 'form_versions.id')->where('forms.key', 'project_enquiry')->select('form_versions.*')->firstOrFail();
    }

    public function draft(array $schema, int $actor): int
    {
        return DB::transaction(function () use ($schema, $actor) {
            $form = DB::table('forms')->where('key', 'project_enquiry')->lockForUpdate()->firstOrFail();
            $this->check($schema, $this->previousFields($form->id));

            return DB::table('form_versions')->insertGetId(['form_id' => $form->id, 'version_number' => 1 + DB::table('form_versions')->where('form_id', $form->id)->max('version_number'), 'schema' => json_encode($schema, JSON_THROW_ON_ERROR), 'created_by' => $actor]);
        });
    }

    public function publish(int $id): void
    {
        DB::transaction(function () use ($id) {
            $form = DB::table('forms')->where('key', 'project_enquiry')->lockForUpdate()->firstOrFail();
            $draft = DB::table('form_versions')->where(['id' => $id, 'form_id' => $form->id, 'status' => 'draft'])->firstOrFail();
            $this->check(json_decode($draft->schema, true), $this->previousFields($form->id));
            DB::table('form_versions')->where('id', $form->active_version_id)->update(['status' => 'retired', 'retired_at' => now()]);
            DB::table('form_versions')->where('id', $id)->update(['status' => 'published', 'published_at' => now()]);
            DB::table('forms')->where('id', $form->id)->update(['active_version_id' => $id]);
            Audit::record('published', 'form_version', $id);
        });
    }

    private function previousFields(int $formId): array
    {
        return DB::table('form_versions')->where('form_id', $formId)->whereIn('status',['published', 'retired'])
            ->pluck('schema')->flatMap(fn ($schema) => json_decode($schema,true))->all();
    }
}
