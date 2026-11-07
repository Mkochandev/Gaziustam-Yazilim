@props(['path', 'alt' => '', 'loading' => 'lazy'])
@php
    $disk = \Illuminate\Support\Facades\Storage::disk('public');
    $source = $disk->exists($path.'.webp') ? $path.'.webp' : $path;
    $dimensions = $disk->exists($source) ? @getimagesize($disk->path($source)) : false;
@endphp
@if($dimensions)
<img src="{{ $disk->url($source) }}" width="{{ $dimensions[0] }}" height="{{ $dimensions[1] }}" alt="{{ $alt }}" loading="{{ $loading }}" {{ $attributes->merge(['style'=>'max-width:100%;height:auto']) }}>
@else
<div class="journal-cover" role="img" aria-label="{{ $alt }}"><x-studio-star /></div>
@endif

