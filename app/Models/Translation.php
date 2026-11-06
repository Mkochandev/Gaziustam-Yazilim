<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Translation extends Model
{
    protected $guarded=['id'];
    protected $casts=['published_at'=>'datetime'];
    public function content() { return $this->belongsTo(Content::class); }
}
