<?php

namespace App\Services;

use App\Models\Translation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class Publication
{
    public function visible()
    {
        return Translation::query()->join('contents', 'contents.id', '=', 'translations.content_id')
            ->join('locales', 'locales.code', '=', 'translations.locale')->where('locales.is_active', true)
            ->whereNull('contents.archived_at')->whereNull('contents.deleted_at')
            ->where('translations.status', 'published')->where('published_at', '<=', now())
            ->select('translations.*', 'contents.category', 'contents.type', 'contents.is_concept', 'contents.cover', 'contents.sort_order', 'contents.featured', 'contents.provider');
    }

    public function listing(string $locale, bool $projects, array $filters = [])
    {
        $query = $this->visible()->where('contents.type', $projects ? '=' : '!=', 'project');
        if (($filters['language'] ?? 'current') === 'all' && ! $projects) {
            $ranked = (clone $query)->select('translations.id')->selectRaw(
                "ROW_NUMBER() OVER (PARTITION BY content_id ORDER BY CASE WHEN translations.locale = ? THEN 0 WHEN translations.locale = contents.source_locale THEN 1 ELSE 2 END, CASE translations.locale WHEN 'de' THEN 0 WHEN 'en' THEN 1 WHEN 'tr' THEN 2 ELSE 3 END, locales.sort_order, translations.id) AS priority", [$locale]);
            $query->whereIn('translations.id', DB::query()->fromSub($ranked, 'ranked')->select('id')->where('priority', 1));
        } else {
            $query->where('translations.locale', $locale);
        }
        foreach (['type', 'category'] as $key) {
            if (! empty($filters[$key])) {
                $query->where("contents.$key", $filters[$key]);
            }
        }
        if (! empty($filters['q'])) {
            $query->where(fn ($q) => $q->where('title', 'like', '%'.$filters['q'].'%')->orWhere('excerpt', 'like', '%'.$filters['q'].'%'));
        }
        if ($projects) {
            $query->orderByDesc('featured')->orderBy('contents.sort_order');
        }

        return $query->orderByDesc('published_at')->orderByDesc('translations.id');
    }

    public function save(int $contentId, string $locale, array $data): Translation
    {
        return DB::transaction(function () use ($contentId, $locale, $data) {
            DB::table('contents')->where('id', $contentId)->lockForUpdate()->firstOrFail();
            $translation = Translation::firstOrNew(['content_id' => $contentId, 'locale' => $locale]);
            $reserved = DB::table('slug_entries')->where(['locale' => $locale, 'slug' => $data['slug']])->first();
            if ($reserved && $reserved->translation_id != $translation->id) {
                throw ValidationException::withMessages(['slug' => 'Slug kullanımda.']);
            }
            $config = (new HtmlSanitizerConfig)->allowSafeElements()->allowLinkSchemes(['http', 'https', 'mailto'])->allowRelativeLinks()->allowRelativeMedias();
            $data['body'] = (new HtmlSanitizer($config))->sanitize($data['body'] ?? '');
            $translation->fill($data)->save();
            DB::table('slug_entries')->updateOrInsert(['locale' => $locale, 'slug' => $data['slug']], ['translation_id' => $translation->id]);
            Audit::record('saved', 'translation', $translation->id);

            return $translation;
        });
    }
}
