<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Content extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $casts = ['technologies'=>'array','gallery'=>'array','featured'=>'boolean','is_concept'=>'boolean'];
    public function translations() { return $this->hasMany(Translation::class); }
}
