<?php

namespace Tests\Feature;

use App\Jobs\SendEnquiryNotification;
use App\Livewire\TranslationEditor;
use App\Models\Content;
use App\Models\Locale;
use App\Models\Media;
use App\Models\User;
use App\Services\FormSchema;
use App\Services\Publication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class StudioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function user(string $role): User
    {
        $u = User::factory()->create();
        $u->role = $role;
        $u->save();

        return $u;
    }

    private function content(string $type = 'news'): Content
    {
        return Content::create(['type' => $type, 'source_locale' => 'en', 'category' => 'web']);
    }

    private function translation($c, $l, $slug, $status = 'published', $time = null)
    {
        return app(Publication::class)->save($c->id, $l, ['title' => $slug, 'slug' => $slug, 'body' => '<p>Text</p>', 'status' => $status, 'published_at' => $time ?? now()->subMinute()]);
    }

    private function payload(): array
    {
        return ['version_id' => app(FormSchema::class)->active()->id, 'idempotency_key' => (string) Str::uuid(), 'answers' => ['contact_name' => 'Test', 'contact_email' => 'person@example.test', 'service' => 'web', 'message' => 'A project brief']];
    }

    public function test_site_and_locale_routes(): void
    {
        $this->get('/')->assertRedirect('/de');
        foreach (['de', 'en', 'tr'] as $locale) {
            $this->get("/$locale")->assertOk()->assertSee("lang=\"$locale\"", false)->assertSee('name="version_id"', false);
            foreach (['blog', 'projects', 'privacy', 'legal'] as $path) {
                $this->get("/$locale/$path")->assertOk();
            }
        }
        $this->get('/fr')->assertNotFound();
        $this->get('/zz/blog')->assertNotFound();
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
    }

    public function test_language_priority_future_archive_and_canonical_history(): void
    {
        $a = $this->content();
        $b = $this->content();
        $de = $this->translation($a, 'de', 'a-de');
        $this->translation($a, 'tr', 'a-tr');
        $this->translation($a, 'en', 'a-draft', 'draft');
        $this->translation($b, 'en', 'b-en');
        $this->get('/de/blog?type=news')->assertOk()->assertSee('/de/blog/a-de')->assertDontSee('/en/blog/b-en');
        $html = $this->get('/de/blog?type=news&language=all&category=web')->assertOk()->assertSee('/en/blog/b-en')->assertDontSee('a-draft')->getContent();
        $this->assertSame(1, substr_count($html, 'href="http://localhost/de/blog/a-de"'));
        $this->get('/en/blog/a-draft')->assertNotFound();
        $this->translation($a, 'de', 'renamed');
        $this->get('/de/blog/a-de')->assertMovedPermanently()->assertRedirect('/de/blog/renamed');
        $this->translation($b, 'en', 'b-en', 'published', now()->addHour());
        $this->get('/sitemap.xml')->assertDontSee('b-en')->assertDontSee('a-draft');
        $this->travel(2)->hours();
        $this->get('/en/blog/b-en')->assertOk();
        $a->update(['archived_at' => now()]);
        $this->get('/de/blog/a-de')->assertNotFound();
    }

    public function test_panel_editor_creates_linked_translations_and_signed_preview(): void
    {
        $u = $this->user('editor');
        $a = $this->content();
        $this->actingAs($u);
        $this->get('/admin/contents')->assertOk();
        $this->get('/admin/contents/create')->assertOk();
        $this->post("/admin/yazilar/$a->id/de", ['title' => 'Nachricht', 'slug' => 'neu', 'status' => 'published', 'body' => '<p onclick="bad()">Text</p><script>EVIL()</script><a href="javascript:bad()">bad</a>'])->assertSessionHasNoErrors();
        $this->get('/de/blog/neu')->assertOk()->assertDontSee('EVIL()')->assertDontSee('onclick="bad()"', false)->assertDontSee('javascript:bad()');
        $t = $this->translation($a, 'tr', 'taslak', 'draft');
        $link = URL::temporarySignedRoute('studio.preview', now()->addMinute(), ['translation' => $t]);
        $this->get($link)->assertOk()->assertHeader('X-Robots-Tag', 'noindex');
        $this->actingAs($this->user('sales'))->get($link)->assertForbidden();
        $this->get('/admin/contents')->assertForbidden();
    }

    public function test_panel_schema_publish_snapshot_and_retry(): void
    {
        $owner = $this->user('owner');
        $data = $this->payload();
        $schema = app(FormSchema::class)->defaults();
        $this->post('/tr/enquiries', $data)->assertSessionHas('reference');
        $this->post('/tr/enquiries', $data)->assertSessionHas('reference');
        $this->assertDatabaseCount('enquiries', 1);
        $this->assertDatabaseCount('jobs', 1);
        $this->actingAs($owner);
        $this->get('/admin/form')->assertOk();
        $this->get('/admin/erisim')->assertOk();
        $schema[0]['labels']['tr'] = 'Yeni etiket';
        $this->post('/admin/form', ['schema' => json_encode(array_reverse($schema))])->assertSessionHasNoErrors();
        $draft = DB::table('form_versions')->where('status', 'draft')->first();
        $this->get('/admin/form?v='.$draft->id)->assertSee('Yeni etiket');
        $this->get('/tr')->assertDontSee('Yeni etiket');
        $this->post("/admin/form/$draft->id/publish")->assertSessionHasNoErrors();
        $this->get('/tr')->assertSee('Yeni etiket');
        $this->get('/admin/talepler')->assertOk();
        $this->get('/admin/talepler/1')->assertOk()->assertSee('Ad soyad')->assertDontSee('Yeni etiket');
        $data['idempotency_key'] = (string) Str::uuid();
        $this->post('/tr/enquiries', $data)->assertSessionHas('reference');
        $this->travel(25)->hours();
        $data['idempotency_key'] = (string) Str::uuid();
        $this->post('/tr/enquiries', $data)->assertSessionHasErrors('version_id');
    }

    public function test_validation_private_file_role_and_team_history(): void
    {
        Storage::fake('local');
        $data = $this->payload();
        $data['answers']['extra'] = 'injected';
        $this->post('/de/enquiries', $data)->assertSessionHasErrors('answers');
        unset($data['answers']['extra']);
        $data['answers']['service'] = 'fake';
        $this->post('/de/enquiries', $data)->assertSessionHasErrors('answers.service');
        $data['answers']['service'] = 'web';
        $data['answers']['attachments'] = [UploadedFile::fake()->create('bad.exe', 1, 'application/octet-stream')];
        $this->post('/de/enquiries', $data)->assertSessionHasErrors('answers.attachments.0');
        $this->assertDatabaseCount('enquiries', 0);
        $data['answers']['attachments'] = [UploadedFile::fake()->image('sample.jpg')];
        $this->post('/de/enquiries', $data)->assertSessionHas('reference');
        $file = DB::table('enquiry_attachments')->first();
        Storage::disk('local')->assertExists($file->path);
        $url = "/admin/talepler/$file->enquiry_id/ek/$file->id";
        $this->actingAs($this->user('editor'))->get($url)->assertForbidden();
        $this->get('/admin/talepler')->assertForbidden();
        $this->get('/admin/form')->assertForbidden();
        $sales = $this->user('sales');
        $this->actingAs($sales)->get($url)->assertDownload('sample.jpg');
        $this->get("/admin/talepler/999/ek/$file->id")->assertNotFound();
        $this->post("/admin/talepler/$file->enquiry_id", ['status' => 'review', 'assigned_to' => $sales->id, 'note' => 'İç not'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('enquiry_events', ['old_status' => 'new', 'new_status' => 'review', 'old_assigned_to' => null, 'new_assigned_to' => $sales->id, 'note' => 'İç not']);
    }

    public function test_scoped_api_is_idempotent_and_revocable(): void
    {
        $user = $this->user('owner');
        $token = $user->createToken('writer', ['posts:write']);
        $this->getJson('/api/v1/posts')->assertUnauthorized();
        $input = ['type' => 'news', 'source_locale' => 'de', 'provider' => 'test', 'external_id' => 'abc'];
        $response = $this->withToken($token->plainTextToken)->postJson('/api/v1/posts', $input)->assertCreated();
        $id = $response->json('id');
        $this->postJson('/api/v1/posts', $input)->assertOk()->assertJsonPath('id', $id);
        $path = "/api/v1/posts/$id/translations/de";
        $this->putJson($path, ['title' => 'Test', 'slug' => 'api-post', 'body' => '<p>Text</p><script>evil()</script>'])->assertCreated()->assertJsonPath('status', 'draft');
        $this->postJson($path.'/publish')->assertForbidden();
        $this->get('/de/blog/api-post')->assertNotFound();
        $publisher = $user->createToken('publisher', ['posts:publish']);
        auth()->forgetGuards();
        $this->withToken($publisher->plainTextToken)->postJson($path.'/publish')->assertOk();
        $this->get('/de/blog/api-post')->assertOk()->assertDontSee('evil()');
        $publisher->accessToken->delete();
        auth()->forgetGuards();
        $this->postJson($path.'/publish')->assertUnauthorized();
    }

    public function test_rich_editor_and_resource_entry_points(): void
    {
        $owner = $this->user('owner');
        $content = $this->content('project');
        $this->actingAs($owner);
        foreach (['/admin', '/admin/media', '/admin/media/create', '/admin/categories', '/admin/locales', "/admin/yazilar/$content->id/de"] as $path) {
            $this->get($path)->assertOk();
        }
        Livewire::test(TranslationEditor::class, ['contentId' => $content->id, 'locale' => 'de'])
            ->set('data.title', 'Panel project')->set('data.slug', 'panel-project')
            ->set('data.body', '<p>Published through the editor.</p>')
            ->set('data.status', 'published')->call('save')->assertHasNoErrors();
        $this->get('/de/projects/panel-project')->assertOk()->assertSee('Panel project');
        $this->get('/de')->assertSee('Panel project');
        $this->actingAs($this->user('sales'));
        Livewire::test(TranslationEditor::class, ['contentId' => $content->id, 'locale' => 'de'])->assertForbidden();
        $this->get('/admin/media')->assertForbidden();
    }

    public function test_invalid_schemas_cannot_publish_and_incomplete_languages_cannot_enable(): void
    {
        $owner = $this->user('owner');
        $this->actingAs($owner);
        $schema = app(FormSchema::class)->defaults();
        $schema[0]['type'] = 'textarea';
        $this->post('/admin/form', ['schema' => json_encode($schema)])->assertSessionHasErrors('schema');
        $schema = app(FormSchema::class)->defaults();
        unset($schema[1]['labels']['de']);
        $this->post('/admin/form', ['schema' => json_encode($schema)])->assertSessionHasErrors('schema');
        $schema = app(FormSchema::class)->defaults();
        $schema[1]['validation'] = 'sometimes|sql';
        $this->post('/admin/form', ['schema' => json_encode($schema)])->assertSessionHasErrors('schema');
        $this->assertDatabaseCount('form_versions', 1);
        $locale = Locale::findOrFail('fr');
        try {
            $locale->update(['is_active' => true]);
            $this->fail('Incomplete locale enabled');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('is_active', $e->errors());
        }
        $this->get('/fr')->assertNotFound();
    }

    public function test_mail_failure_does_not_lose_enquiry_and_job_can_retry(): void
    {
        $data = $this->payload();
        $this->post('/de/enquiries', $data)->assertSessionHas('reference');
        $reference = DB::table('enquiries')->value('public_reference');
        config(['studio.notify' => 'team@example.test']);
        Mail::shouldReceive('raw')->once()->andThrow(new \RuntimeException('SMTP unavailable'));
        try {
            (new SendEnquiryNotification($reference))->handle();
            $this->fail('Expected mail error');
        } catch (\RuntimeException $e) {
            $this->assertSame('SMTP unavailable', $e->getMessage());
        }
        $this->assertDatabaseHas('enquiries', ['public_reference' => $reference]);
        $this->assertDatabaseCount('jobs', 1);
        Mail::swap(new class
        {
            public function raw($text, $callback)
            {
                if (! str_contains($text, '/admin/talepler')) {
                    throw new \RuntimeException('Missing link');
                }
            }
        });
        (new SendEnquiryNotification($reference))->handle();
        $this->post('/de/enquiries', $data)->assertSessionHas('reference');
        $this->assertDatabaseCount('enquiries', 1);
        $this->assertDatabaseCount('jobs', 1);
    }

    public function test_media_in_use_is_protected_and_slug_history_is_reserved(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('cover.jpg');
        $path = $file->store('covers', 'public');
        $content = $this->content();
        $content->update(['cover' => $path]);
        $media = Media::where('path', $path)->firstOrFail();
        Storage::disk('public')->assertExists($path.'.webp');
        $this->assertTrue($media->inUse());
        try {
            $media->delete();
            $this->fail('Used media deleted');
        } catch (ValidationException $e) {
            Storage::disk('public')->assertExists($path);
        }
        $this->translation($content, 'de', 'reserved');
        $this->translation($content, 'de', 'new-url');
        try {
            $this->translation($this->content(), 'de', 'reserved');
            $this->fail('History was reused');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('slug', $e->errors());
        }
    }

    public function test_failed_queue_insert_rolls_back_records_and_removes_uploaded_files(): void
    {
        Storage::fake('local');
        $data = $this->payload();
        $data['answers']['attachments'] = [UploadedFile::fake()->image('sample.jpg')];
        DB::listen(function ($event) {
            if (str_contains($event->sql, 'insert into') && str_contains($event->sql, 'jobs')) {
                throw new \RuntimeException('Simulated queue write failure');
            }
        });
        $this->withoutExceptionHandling();
        try {
            $this->post('/de/enquiries', $data);
            $this->fail('Expected transaction failure');
        } catch (\RuntimeException $e) {
            $this->assertSame('Simulated queue write failure', $e->getMessage());
        }
        $this->assertDatabaseCount('enquiries', 0);
        $this->assertDatabaseCount('enquiry_answers', 0);
        $this->assertDatabaseCount('jobs', 0);
        $this->assertSame([], Storage::disk('local')->allFiles('enquiries'));
    }

    public function test_owner_access_changes_are_protected(): void
    {
        $owner = $this->user('owner');
        $editor = $this->user('editor');
        $token = $editor->createToken('test', ['posts:write']);
        $this->actingAs($owner);
        $this->patch("/admin/users/$owner->id", ['role' => 'sales', 'active' => 1])->assertSessionHasErrors('role');
        $this->patch("/admin/users/$editor->id", ['role' => 'editor', 'active' => 0])->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $token->accessToken->id]);
        $this->actingAs($editor->fresh())->get('/admin/contents')->assertForbidden();
        $this->get('/admin/talepler')->assertForbidden();
    }
}
