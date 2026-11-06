@extends('layout')
@section('title',__('forms.'.$section).' · GaziUstam')
@section('content')
<header class="wrap content-header"><h1>{{ __('forms.'.$section) }}</h1><p>{{ __('forms.intro') }}</p></header>
<section class="wrap content-section">
<form method="get" class="listing-filters">
<label>{{ __('forms.search') }} <input type="search" name="q" value="{{ $filters['q']??'' }}"></label>
<label>{{ __('forms.category') }} <select name="category"><option value="">—</option>@foreach(app(\App\Services\FormSchema::class)->defaults()[4]['options'] as $o)<option value="{{ $o['value'] }}" @selected(($filters['category']??'')===$o['value'])>{{ $o['labels'][$locale] }}</option>@endforeach</select></label>
@if($section==='blog')
<label>{{ __('forms.type') }} <select name="type"><option value="">—</option>@foreach(['news','article'] as $type)<option value="{{ $type }}" @selected(($filters['type']??'')===$type)>{{ __('forms.'.$type) }}</option>@endforeach</select></label>
<input type="hidden" name="language" value="{{ $filters['language']??'current' }}">
@endif
<button class="btn">{{ __('forms.filter') }}</button>
</form>
@if($section==='blog')
@php($current=($filters['language']??'current')==='current')
<a class="filter {{ $current?'active':'' }}" href="{{ url()->query(request()->path(),array_merge($filters,['language'=>$current?'all':'current'])) }}">{{ $current?'✓':'○' }} {{ __('forms.only') }}</a>
@endif
<div class="{{ $section==='projects'?'work-grid':'journal-grid' }}">
@forelse($items as $item)
<a class="{{ $section==='projects'?'work-card':'journal-card' }}" href="{{ url("/$item->locale/$section/$item->slug") }}" lang="{{ $item->locale }}">
@if($item->cover)<img src="{{ asset('storage/'.$item->cover) }}" alt="{{ $item->cover_alt }}" loading="lazy" style="width:100%;aspect-ratio:16/9;object-fit:cover">
@elseif($section==='projects')<x-project-art :category="$item->category"/>
@else<div class="journal-cover"><div class="journal-art journal-{{ $loop->index%3 }}"><x-studio-star/></div></div>@endif
<small>{{ strtoupper($item->locale) }} · {{ $item->is_concept?__('forms.concept'):($section==='blog'?__('forms.'.$item->type):$item->category) }} · {{ $item->published_at->format('d.m.Y') }}</small><h2>{{ $item->title }}</h2><p>{{ $item->excerpt }}</p>
</a>
@empty<p class="empty">{{ __('forms.empty') }} @if($section==='blog')<a href="{{ url()->query(request()->path(),array_merge($filters,['language'=>'all'])) }}">{{ __('forms.all') }}</a>@endif</p>@endforelse
</div>
<nav>{{ $items->links('pagination::simple-default') }}</nav>
</section>
@endsection
