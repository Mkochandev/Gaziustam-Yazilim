<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('role')->default('editor');
            $t->boolean('active')->default(true);
        });
        Schema::create('locales', function (Blueprint $t) {
            $t->string('code', 16)->primary();
            $t->string('native_name');
            $t->string('direction')->default('ltr');
            $t->boolean('is_active')->default(false);
            $t->unsignedInteger('sort_order');
        });
        Schema::create('contents', function (Blueprint $t) {
            $t->id();
            $t->string('type'); // article, news or project
            $t->string('source_locale', 16);
            $t->string('category')->nullable();
            $t->string('cover')->nullable();
            $t->string('provider')->nullable();
            $t->string('external_id')->nullable();
            $t->unique(['provider','external_id']);
            $t->string('project_url')->nullable();
            $t->json('technologies')->nullable();
            $t->json('gallery')->nullable();
            $t->boolean('is_concept')->default(false);
            $t->boolean('featured')->default(false);
            $t->integer('sort_order')->default(0);
            $t->timestamp('archived_at')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('translations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('content_id')->constrained()->cascadeOnDelete();
            $t->string('locale',16);
            $t->string('title');
            $t->string('slug',180);
            $t->text('excerpt')->nullable();
            $t->longText('body')->nullable();
            foreach (['problem','solution','deliverables','cover_alt','meta_title','meta_description'] as $field) $t->text($field)->nullable();
            $t->string('status')->default('draft');
            $t->timestamp('published_at')->nullable();
            $t->unique(['content_id','locale']);
            $t->unique(['locale','slug']);
            $t->index(['locale','status','published_at']);
            $t->timestamps();
        });
        Schema::create('slug_entries', function (Blueprint $t) {
            $t->id();
            $t->foreignId('translation_id')->constrained()->cascadeOnDelete();
            $t->string('locale',16);
            $t->string('slug',180);
            $t->unique(['locale','slug']);
        });
        Schema::create('forms', function (Blueprint $t) {
            $t->id(); $t->string('key')->unique(); $t->unsignedBigInteger('active_version_id')->nullable();
        });
        Schema::create('form_versions', function (Blueprint $t) {
            $t->id(); $t->foreignId('form_id')->constrained();
            $t->unsignedInteger('version_number'); $t->unsignedInteger('schema_version')->default(1);
            $t->string('status')->default('draft'); $t->json('schema');
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('published_at')->nullable(); $t->timestamp('retired_at')->nullable();
            $t->unique(['form_id','version_number']);
        });
        Schema::create('enquiries', function (Blueprint $t) {
            $t->id(); $t->string('public_reference')->unique(); $t->uuid('idempotency_key')->unique();
            $t->foreignId('form_version_id')->constrained(); $t->string('locale',16);
            $t->string('contact_name'); $t->string('contact_email'); $t->string('company')->nullable();
            $t->string('service_code')->nullable(); $t->string('budget_code')->nullable();
            $t->string('status')->default('new'); $t->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $t->json('schema_snapshot'); $t->timestamp('submitted_at')->index();
        });
        Schema::create('enquiry_answers', function (Blueprint $t) {
            $t->id(); $t->foreignId('enquiry_id')->constrained()->cascadeOnDelete(); $t->string('field_key'); $t->json('answer'); $t->unique(['enquiry_id','field_key']);
        });
        Schema::create('enquiry_attachments', function (Blueprint $t) {
            $t->id(); $t->foreignId('enquiry_id')->constrained()->cascadeOnDelete();
            $t->string('field_key'); $t->string('path'); $t->string('original_name'); $t->string('mime'); $t->unsignedInteger('size');
        });
        Schema::create('enquiry_events', function (Blueprint $t) {
            $t->id(); $t->foreignId('enquiry_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('old_status')->nullable(); $t->string('new_status')->nullable(); $t->text('note')->nullable(); $t->timestamp('created_at');
        });
    }
    public function down(): void
    {
        foreach (['enquiry_events','enquiry_attachments','enquiry_answers','enquiries','form_versions','forms','slug_entries','translations','contents','locales'] as $table) Schema::dropIfExists($table);
        Schema::table('users', fn(Blueprint $t) => $t->dropColumn(['role','active']));
    }
};
