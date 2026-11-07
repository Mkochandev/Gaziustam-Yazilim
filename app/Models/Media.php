<?php

namespace App\Models;

use App\Services\MediaPreview;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class Media extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::saving(function (Media $media) {
            if ($media->exists && $media->isDirty('path')) {
                throw ValidationException::withMessages(['path' => 'Yeni dosya için yeni medya kaydı oluşturun.']);
            }
            $media->disk = 'public';
            $disk = Storage::disk('public');
            if (! $disk->exists($media->path)) {
                throw ValidationException::withMessages(['path' => 'Dosya bulunamadı.']);
            }
            $media->mime = $disk->mimeType($media->path);
            $media->size = $disk->size($media->path);
            $dimensions = @getimagesize($disk->path($media->path));
            $media->width = $dimensions[0] ?? null;
            $media->height = $dimensions[1] ?? null;
            $media->uploader_id ??= auth()->id();
        });
        static::saved(function (Media $media) {
            MediaPreview::create($media->path, (int) $media->width, (int) $media->height);
        });
        static::deleting(function (Media $media) {
            if ($media->inUse()) {
                throw ValidationException::withMessages(['media' => 'Dosya içerikte kullanılıyor. Önce ilişkisini kaldırın.']);
            }
            if (! Storage::disk('public')->delete($media->path)) {
                throw new \RuntimeException('Dosya silinemedi.');
            }
            Storage::disk('public')->delete($media->path.'.webp');
        });
    }

    public function inUse(): bool
    {
        return Content::withTrashed()->where('cover', $this->path)->exists()
            || Content::withTrashed()->whereJsonContains('gallery', $this->path)->exists()
            || Translation::where('body', 'like', '%'.$this->path.'%')->exists();
    }
}
