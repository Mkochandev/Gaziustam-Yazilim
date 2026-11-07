<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiry_events', function (Blueprint $table) {
            $table->foreignId('old_assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('new_assigned_to')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('enquiry_events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('old_assigned_to');
            $table->dropConstrainedForeignId('new_assigned_to');
        });
    }
};
