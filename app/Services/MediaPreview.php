<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class MediaPreview
{
    public static function create(string $path, int $width, int $height): void
    {
        // Keep decoding within a bounded memory budget. Large originals remain usable.
        if (! $width || ! $height || $width * $height > 8000000 || ! function_exists('imagewebp')) {
            return;
        }
        $disk = Storage::disk('public');
        if ($disk->exists($path.'.webp')) {
            return;
        }
        $image = @imagecreatefromstring($disk->get($path));
        if (! $image) {
            return;
        }
        $targetWidth = min(1600, $width);
        $targetHeight = max(1, (int) round($height * $targetWidth / $width));
        $preview = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($preview, false);
        imagesavealpha($preview, true);
        imagecopyresampled($preview, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        ob_start();
        imagewebp($preview, null, 82);
        $bytes = ob_get_clean();
        imagedestroy($image);
        imagedestroy($preview);
        if ($bytes !== false) {
            $disk->put($path.'.webp',$bytes);
        }
    }
}
