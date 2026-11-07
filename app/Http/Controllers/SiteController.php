<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\FormSchema;
use App\Services\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteController
{
    public function __construct(private Publication $publication) {}

    public function home(string $locale, FormSchema $forms)
    {
        $posts = $this->publication->listing($locale, false)->limit(3)->get();
        $projects = $this->publication->listing($locale, true)->limit(6)->get();
        $version = $forms->active();

        return view('home', compact('posts', 'projects', 'version'));
    }

    public function index(Request $r, string $locale, string $section)
    {
        $filters = $r->validate(['language' => 'nullable|in:all,current', 'type' => 'nullable|in:news,article', 'category' => 'nullable|string|max:50', 'q' => 'nullable|string|max:120']);
        $items = $this->publication->listing($locale, $section === 'projects', $filters)->paginate(9)->withQueryString();
        $categories = Category::with('translations')->whereIn('scope', ['all', $section])->get();

        return view('listing', compact('items', 'section', 'filters', 'categories'));
    }

    public function show(string $locale, string $section, string $slug)
    {
        $item = $this->publication->visible()->where('contents.type', $section === 'projects' ? '=' : '!=', 'project')->where('translations.locale', $locale)->where('slug', $slug)->first();
        if (! $item) {
            $target = $this->publication->visible()->join('slug_entries', 'slug_entries.translation_id', '=', 'translations.id')->where('slug_entries.locale', $locale)->where('slug_entries.slug', $slug)->where('contents.type', $section === 'projects' ? '=' : '!=', 'project')->firstOrFail();

            return redirect("/$locale/$section/".$target->slug, 301);
        }
        $alternates = $this->publication->visible()->where('content_id', $item->content_id)->get()->mapWithKeys(fn ($t) => [$t->locale => url("/$t->locale/$section/$t->slug")])->all();

        return view('detail', ['item' => $item, 'section' => $section, 'alternates' => $alternates, 'listingPath' => $section]);
    }

    public function sitemap()
    {
        $paths = [];
        foreach (DB::table('locales')->where('is_active', true)->pluck('code') as $l) {
            foreach (['', '/blog', '/projects'] as $part) {
                $paths[] = "/$l$part";
            }
        }
        foreach ($this->publication->visible()->get() as $t) {
            $paths[] = "/$t->locale/".($t->type === 'project' ? 'projects' : 'blog')."/$t->slug";
        }

        return response()->view('sitemap',compact('paths'))->header('Content-Type','application/xml');
    }
}
