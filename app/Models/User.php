<?php
namespace App\Models;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable implements FilamentUser
{
    use HasFactory,Notifiable,HasApiTokens;
    protected $attributes=['active'=>true,'role'=>'editor'];
    protected $fillable=['name','email','password'];
    protected $hidden=['password','remember_token'];
    protected $casts=['password'=>'hashed','active'=>'boolean'];
    public function canAccessPanel(Panel $panel): bool { return $this->active && in_array($this->role,['owner','editor','sales'],true); }
}

