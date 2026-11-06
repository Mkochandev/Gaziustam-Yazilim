@extends('layout')
@section('title',($item->meta_title?:$item->title).' · GaziUstam')
@section('head')
<meta name="description" content="{{ $item->meta_description?:$item->excerpt }}">
@if($section==='blog')
<script type="application/ld+json">{!! json_encode(['@context'=>'https://schema.org','@type'=>$item->type==='news'?'NewsArticle':'Article','headline'=>$item->title,'datePublished'=>$item->published_at?->toAtomString(),'inLanguage'=>$locale],JSON_HEX_TAG|JSON_HEX_AMP) !!}</script>
@endif
@endsection
@section('content')
<article class="article"><a href="{{ url("/$locale/$section") }}">← {{ __('forms.'.$section) }}</a><h1>{{ $item->title }}</h1>
<p class="article-meta">{{ strtoupper($locale) }} · {{ $item->published_at?->format('d.m.Y') }} @if($item->is_concept) · {{ __('forms.concept') }} @endif</p>
<p class="excerpt">{{ $item->excerpt }}</p>
@if($item->cover)<img src="{{ asset('storage/'.$item->cover) }}" alt="{{ $item->cover_alt }}" style="max-width:100%">@elseif($section==='projects')<x-project-art :category="$item->category"/>@endif
<div class="article-body">{!! $item->body !!}</div>
@if($section==='projects')
@foreach(['problem','solution','deliverables'] as $key)@if($item->$key)<h2>{{ __('forms.'.$key) }}</h2><p>{{ $item->$key }}</p>@endif @endforeach
<p>{{ implode(' · ',$item->content->technologies??[]) }}</p>
@foreach($item->content->gallery??[] as $photo)<img loading="lazy" style="max-width:100%" src="{{ asset('storage/'.$photo) }}" alt="{{ $item->title }}">@endforeach
@if($item->content->project_url)<p><a href="{{ $item->content->project_url }}" rel="noopener noreferrer">{{ __('forms.site') }} ↗</a></p>@endif
<a class="btn" href="{{ url("/$locale?service=".$item->category).'#kontakt' }}">{{ __('forms.send') }}</a>@endif
</article>
@endsection
