<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('scope')->default('all');
        });
        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 16);
            $table->string('name');
            $table->string('slug', 180);
            $table->unique(['category_id', 'locale']);
            $table->unique(['locale', 'slug']);
        });
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('disk')->default('public');
            $table->string('path')->unique();
            $table->string('mime');
            $table->unsignedBigInteger('size');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->foreignId('uploader_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('subject_type');
            $table->string('subject_id');
            $table->timestamp('created_at');
        });
        Schema::table('contents', function (Blueprint $table) {
            $table->string('source_url', 2048)->nullable();
            $table->string('client_display_name')->nullable();
        });
        Schema::table('translations', function (Blueprint $table) {
            $table->json('gallery_alts')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('translations', fn (Blueprint $table) => $table->dropColumn('gallery_alts'));
        Schema::table('contents', fn (Blueprint $table) => $table->dropColumn(['source_url', 'client_display_name']));
        foreach (['activity_logs', 'media', 'category_translations', 'categories'] as $name) {
            Schema::dropIfExists($name);
        }
    }
};
