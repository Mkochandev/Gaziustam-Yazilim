<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

Artisan::command('studio:owner {email}', function () {
    $email = $this->argument('email');
    $name = $this->ask('İsim');
    $password = $this->secret('Parola (en az 12 karakter)');
    Validator::make(compact('email', 'name', 'password'), ['email' => 'required|email|unique:users', 'name' => 'required|max:255', 'password' => 'required|min:12'])->validate();
    $user = new User(compact('name', 'email', 'password'));
    $user->role = 'owner';
    $user->save();
    $this->info('Owner hesabı oluşturuldu.');
});
Artisan::command('studio:prune', function () {
    $days = config('studio.retention_days');
    if ($days < 1) {
        throw new RuntimeException('Pozitif saklama süresi gerekli.');
    }
    DB::table('enquiries')->where('submitted_at', '<', now()->subDays($days))->chunkById(100, function ($rows) {
        foreach ($rows as $row) {
            foreach (DB::table('enquiry_attachments')->where('enquiry_id', $row->id)->pluck('path') as $path) {
                if (! Storage::disk('local')->delete($path)) {
                    throw new RuntimeException('Ek silinemedi.');
                }
            }
            DB::table('enquiries')->where('id', $row->id)->delete();
        }
    });
});
Schedule::command('studio:prune')->daily()->withoutOverlapping();
