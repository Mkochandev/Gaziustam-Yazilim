<?php

namespace App\Models;

use App\Services\FormSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class Locale extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    protected $guarded = ['code'];

    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(function (Locale $locale) {
            $error = fn ($message) => throw ValidationException::withMessages(['is_active' => $message]);
            if ($locale->code === 'de' && ! $locale->is_active) {
                $error('Varsayılan Almanca dili etkin kalmalıdır.');
            }
            if (! $locale->is_active) {
                return;
            }
            foreach (['studio', 'forms', 'validation'] as $file) {
                $path = lang_path("$locale->code/$file.php");
                if (! is_file($path)) {
                    $error("$file arayüz çevirisi eksik.");
                }
                $reference = Arr::dot(require lang_path("de/$file.php"));
                $translation = Arr::dot(require $path);
                foreach ($reference as $key => $value) {
                    if (! isset($translation[$key]) || ! is_string($translation[$key]) || trim($translation[$key]) === '') {
                        $error("$file.$key çevirisi eksik.");
                    }
                }
            }
            foreach (json_decode(app(FormSchema::class)->active()->schema, true) as $field) {
                if (empty($field['labels'][$locale->code])) {
                    $error($field['key'].' form etiketi eksik.');
                }
                foreach ($field['options'] ?? [] as $option) {
                    if (empty($option['labels'][$locale->code])) {
                        $error($field['key'].' seçenek çevirisi eksik.');
                    }
                }
            }
        });
    }
}
