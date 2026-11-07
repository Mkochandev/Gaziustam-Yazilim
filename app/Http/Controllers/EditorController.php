<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Translation;
use App\Services\ContentInput;
use App\Services\FormSchema;
use App\Services\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EditorController
{
    public function edit(Content $content, string $locale)
    {
        abort_unless(DB::table('locales')->where('code', $locale)->exists(), 404);
        $translation = $content->translations()->firstOrNew(['locale' => $locale]);
        $editingLocales = DB::table('locales')->where('is_active', true)
            ->orWhereIn('code', $content->translations()->pluck('locale'))->orWhere('code', $locale)->orderBy('sort_order')->get();

        return view('admin.translation', compact('content', 'translation', 'locale', 'editingLocales'));
    }

    public function save(Request $r, Content $content, string $locale, Publication $service)
    {
        abort_unless(DB::table('locales')->where('code', $locale)->exists(), 404);
        $data = ContentInput::translation($r);
        if (($data['status'] ?? 'draft') === 'published') {
            if (! trim(strip_tags($data['body'] ?? ''))) {
                throw ValidationException::withMessages(['body' => 'Yayın için gövde gerekli.']);
            }
            $data['published_at'] ??= now();
        }
        $service->save($content->id, $locale, $data);

        return back()->with('notice', 'Dil sürümü kaydedildi.');
    }

    public function preview(Translation $translation)
    {
        return response()->view('admin.preview', compact('translation'))->header('X-Robots-Tag', 'noindex')->header('Cache-Control', 'private, no-store');
    }

    public function form(Request $r, FormSchema $forms)
    {
        $version = $r->query('v') ? DB::table('form_versions')->find($r->query('v')) : $forms->active();
        abort_unless($version, 404);
        $versions = DB::table('form_versions')->orderByDesc('version_number')->get();
        $locale = in_array($r->query('lang'), ['de', 'en', 'tr']) ? $r->query('lang') : 'tr';

        return view('admin.form', compact('version', 'versions', 'locale'));
    }

    public function draft(Request $r, FormSchema $forms)
    {
        $raw = $r->validate(['schema' => 'required|json|max:180000'])['schema'];
        $schema = json_decode($raw, true);
        if (! is_array($schema)) {
            throw ValidationException::withMessages(['schema' => 'Alan listesi gerekli.']);
        }
        $id = $forms->draft($schema, $r->user()->id);

        return redirect("/admin/form?v=$id")->with('notice', 'Taslak kaydedildi.');
    }

    public function publish(int $version, FormSchema $forms)
    {
        $forms->publish($version);

        return redirect('/admin/form')->with('notice', 'Sürüm yayınlandı.');
    }
}
