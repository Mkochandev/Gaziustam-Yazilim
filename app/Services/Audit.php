<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class Audit
{
    public static function record(string $action, string $type, int|string $id): void
    {
        DB::table('activity_logs')->insert([
            'actor_id' => auth()->id(), 'action' => $action,
            'subject_type' => $type, 'subject_id' => (string) $id, 'created_at' => now(),
        ]);
    }
}
