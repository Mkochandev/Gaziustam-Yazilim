<?php

namespace App\Models;

use App\Services\Audit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Content extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = ['technologies' => 'array', 'gallery' => 'array', 'featured' => 'boolean', 'is_concept' => 'boolean'];

    public function translations()
    {
        return $this->hasMany(Translation::class);
    }

    protected static function booted(): void
    {
        static::saved(function (Content $content) {
            foreach (array_filter(array_merge([$content->cover], $content->gallery ?? [])) as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Media::firstOrCreate(['path' => $path]);
                }
            }
            Audit::record('saved', 'content', $content->id);
        });
        static::deleted(fn (Content $content) => Audit::record('deleted', 'content', $content->id));
    }
}
