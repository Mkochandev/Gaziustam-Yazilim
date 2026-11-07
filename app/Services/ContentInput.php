<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContentInput
{
    public static function translation(Request $r): array
    {
        return $r->validate([
            'title' => 'required|string|max:255', 'slug' => ['required', 'string', 'max:180', 'regex:/^[\pL\pN]+(?:-[\pL\pN]+)*$/u'],
            'excerpt' => 'nullable|string|max:4000', 'body' => 'nullable|string|max:150000',
            'meta_title' => 'nullable|string|max:255', 'meta_description' => 'nullable|string|max:1000',
            'cover_alt' => 'nullable|string|max:255', 'problem' => 'nullable|string|max:10000', 'solution' => 'nullable|string|max:10000', 'deliverables' => 'nullable|string|max:10000',
            'gallery_alts' => 'nullable|array|max:50', 'gallery_alts.*' => 'nullable|string|max:500',
            'status' => ['sometimes', Rule::in(['draft', 'published'])],
            'published_at' => 'nullable|date',
        ]);
    }
}
