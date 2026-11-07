<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Content;
use App\Services\FormSchema;
use App\Services\Publication;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            foreach (file(__DIR__.'/locales.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $i => $line) {
                [$code,$native] = explode('|', $line);
                DB::table('locales')->insertOrIgnore(['code' => $code, 'native_name' => $native, 'sort_order' => $i, 'is_active' => $i < 3]);
            }
            if (! DB::table('forms')->exists()) {
                $id = DB::table('forms')->insertGetId(['key' => 'project_enquiry']);
                $schema = app(FormSchema::class)->defaults();
                app(FormSchema::class)->check($schema);
                $v = DB::table('form_versions')->insertGetId(['form_id' => $id, 'version_number' => 1, 'status' => 'published', 'published_at' => now(), 'schema' => json_encode($schema, JSON_THROW_ON_ERROR)]);
                DB::table('forms')->where('id', $id)->update(['active_version_id' => $v]);
            }
            foreach (app(FormSchema::class)->defaults()[4]['options'] as $option) {
                $category = Category::firstOrCreate(['code' => $option['value']], ['scope' => 'all']);
                foreach ($option['labels'] as $language => $name) {
                    $category->translations()->firstOrCreate(['locale' => $language], ['name' => $name, 'slug' => $option['value']]);
                }
            }
            if (! app()->environment('local', 'testing')) {
                return;
            }
            $demo = json_decode(file_get_contents(__DIR__.'/demo.json'), true, flags: JSON_THROW_ON_ERROR);
            foreach ($demo as $locale => $items) {
                foreach ($items as $i => [$title,$excerpt,$body]) {
                    $content = Content::firstOrCreate(['provider' => 'studio-demo', 'external_id' => (string) $i], ['type' => $i < 3 ? 'article' : 'project', 'category' => ['software', 'ai', 'software', 'software', 'apps', 'commerce'][$i], 'source_locale' => 'de', 'is_concept' => $i >= 3, 'featured' => $i >= 3, 'sort_order' => $i]);
                    if (! $content->translations()->where('locale', $locale)->exists()) {
                        app(Publication::class)->save($content->id, $locale, ['title' => $title, 'slug' => Str::slug($title), 'excerpt' => $excerpt, 'body' => '<p>'.str_replace("\n\n", '</p><p>', e($body)).'</p>', 'status' => 'published', 'published_at' => now()->subDays($i + 1)]);
                    }
                }
            }
        });
    }
}
