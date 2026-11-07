<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        foreach (['content' => ['editor', 'owner'], 'sales' => ['sales', 'owner'], 'manage' => ['owner']] as $ability => $roles) {
            Gate::define($ability, fn (User $u) => $u->active && in_array($u->role, $roles, true));
        }
    }
}
