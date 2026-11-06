@extends('layout')

@section('head')
<meta name="description" content="{{ __('studio.intro') }}">
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => 'GaziUstam', 'url' => url('/de')], JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endsection

@section('content')
@php
    $disciplines = __('studio.disciplines');
    $selection = collect($projects);
    $category = request()->query('category');
    $filtered = $category === null ? $selection : $selection->where('category', $category);
@endphp
<section class="editorial-hero">
    <div class="wrap">
        <div class="hero-kicker"><span><i></i> TÜRKİYE → EUROPE / DIGITAL STUDIO</span><span>DE / AT — EUROPE</span></div>
        <div class="hero-type-grid">
            <h1><span>{{ __('studio.headline') }}</span><em>{{ __('studio.accent') }}</em></h1>
            <div class="hero-intro">
                <span class="intro-star"><x-studio-star /></span>
                <p>{{ __('studio.intro') }}</p>
                <a class="btn" href="#kontakt">{{ __('studio.cta') }}<span aria-hidden="true">↗</span></a>
            </div>
        </div>
        <div class="hero-art-panel">
            <img src="{{ asset('images/chrome-blue-hero.webp') }}"
                 srcset="{{ asset('images/chrome-blue-hero-640.webp') }} 640w, {{ asset('images/chrome-blue-hero-1100.webp') }} 1100w, {{ asset('images/chrome-blue-hero.webp') }} 1942w"
                 sizes="(max-width: 550px) 100vw, (max-width: 1536px) 92vw, 1408px"
                 width="1942" height="809" alt="{{ __('studio.art_alt') }}" fetchpriority="high">
            <div class="hero-art-top"><span>GAZIUSTAM / DIGITAL CRAFT</span><span>SOFTWARE × DESIGN × AI</span></div>
            <div class="hero-art-bottom">
                <div><span>USTA İŞİ.</span><strong>Turkish craft. Digital solutions.</strong></div>
                <a href="#portfolio" aria-label="{{ __('studio.projects') }}">↘</a>
            </div>
        </div>
        <div class="studio-disciplines">
            @foreach(['ai','software','apps','commerce'] as $code)
                @unless($loop->first)<i><x-studio-star /></i>@endunless
                <span>{{ $disciplines[$code]['title'] }}</span>
            @endforeach
        </div>
    </div>
</section>
<section class="studio-work section" id="portfolio">
    <div class="wrap">
        <div class="section-head">
            <div><span class="eyebrow">{{ __('studio.projects') }}</span><h2>{{ __('studio.work_title') }}</h2></div>
            <div class="section-aside"><a class="section-link" href="{{ url($locale.'/projects') }}">{{ __('studio.all_projects') }}<span aria-hidden="true">↗</span></a></div>
        </div>
        @if($selection->isNotEmpty())
            <div class="content-tabs" aria-label="{{ __('studio.projects') }}">
                <a class="filter {{ $category === null ? 'active' : '' }}" href="{{ url($locale).'#portfolio' }}">{{ __('studio.all') }}</a>
                @foreach($selection->pluck('category')->filter()->unique() as $code)
                    <a class="filter {{ $category === $code ? 'active' : '' }}" href="{{ url($locale).'?'.http_build_query(['category' => $code]).'#portfolio' }}">{{ $disciplines[$code]['title'] ?? $code }}</a>
                @endforeach
            </div>
        @endif
        <div class="work-grid">
            @forelse($filtered as $project)
                <article class="work-card">
                    <a class="work-cover" href="{{ url($project->locale.'/projects/'.$project->slug) }}" aria-label="{{ $project->title }}">
                        @if($project->cover ?? null)
                            <img src="{{ asset('storage/'.$project->cover) }}" alt="{{ $project->cover_alt ?? '' }}" loading="lazy">
                        @elseif($project->is_concept)
                            <x-project-art :category="$project->category" />
                        @else
                            <span class="journal-art"><x-studio-star /></span>
                        @endif
                    </a>
                    <div class="work-info">
                        <small>@if($project->is_concept){{ __('studio.concept') }} / @endif{{ $disciplines[$project->category]['title'] ?? $project->category }}</small>
                        <h3>{{ $project->title }}</h3><p>{{ $project->excerpt }}</p>
                        <a href="{{ url($project->locale.'/projects/'.$project->slug) }}">{{ __('studio.read_project') }} · {{ $project->title }}</a>
                    </div>
                </article>
            @empty
                <p class="empty">{{ __('studio.empty') }}</p>
            @endforelse
        </div>
    </div>
</section>
<section class="studio-services section" id="leistungen">
    <div class="wrap service-editorial-grid">
        <div class="service-intro">
            <span class="eyebrow">{{ __('studio.services') }}</span>
            <h2>{{ __('studio.service_title') }}</h2>
            <span class="large-asterisk"><x-studio-star /></span>
        </div>
        <div class="service-rows">
            @foreach($disciplines as $code => $discipline)
                <a class="service-row" href="{{ url($locale).'?service='.$code.'#kontakt' }}">
                    <span class="service-index">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <div><h3>{{ $discipline['title'] }}</h3><p>{{ $discipline['description'] }}</p></div>
                    <span class="service-arrow" aria-hidden="true">↗</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
<section class="studio-process section">
    <div class="wrap">
        <div class="section-head"><h2>{{ __('studio.process') }}</h2></div>
        <div class="process-cards">
            @foreach(['brief','meet','deliver'] as $step)
                <div class="process-card">
                    <div><span>0{{ $loop->iteration }}</span><i aria-hidden="true">↗</i></div>
                    <h3>{{ __('studio.'.$step) }}</h3><span class="process-line"></span>
                </div>
            @endforeach
        </div>
    </div>
</section>
<section class="studio-journal section" id="blog">
    <div class="wrap">
        <div class="section-head">
            <div><span class="eyebrow">{{ __('studio.journal') }}</span><h2>{{ __('studio.journal_title') }}</h2></div>
            <div class="section-aside">
                <p>{{ __('studio.journal_intro') }}</p>
                <a class="section-link" href="{{ url($locale.'/blog') }}">{{ __('studio.all_posts') }}<span aria-hidden="true">↗</span></a>
            </div>
        </div>
        <div class="journal-grid">
            @forelse($posts as $post)
                <article class="journal-card journal-{{ $loop->index % 3 }}">
                    <a class="journal-cover" aria-label="{{ $post->title }}" href="{{ url($post->locale.'/blog/'.$post->slug) }}">
                        @if($post->cover ?? null)
                            <img src="{{ asset('storage/'.$post->cover) }}" loading="lazy" alt="{{ $post->cover_alt ?? '' }}">
                        @else
                            <span class="journal-art" aria-hidden="true">
                                @if($loop->first)<x-studio-star />@elseif($loop->index === 1)↗@else{ }@endif
                            </span>
                            <small>GAZIUSTAM / JOURNAL</small>
                        @endif
                    </a>
                    <div class="journal-meta"><span>{{ $disciplines[$post->category]['title'] ?? $post->category }}</span><span>{{ strtoupper($post->locale) }}</span></div>
                    <h3><a href="{{ url($post->locale.'/blog/'.$post->slug) }}">{{ $post->title }}</a></h3>
                    <p>{{ $post->excerpt }}</p>
                    <a class="journal-link" href="{{ url($post->locale.'/blog/'.$post->slug) }}">{{ __('studio.read_post') }}<span aria-hidden="true">↗</span></a>
                </article>
            @empty
                <p class="empty">{{ __('studio.empty') }}</p>
            @endforelse
        </div>
    </div>
</section>
<section class="contact-section" id="kontakt">
    <div class="wrap contact-layout">
        <div class="contact-copy">
            <span class="eyebrow">{{ __('studio.cta') }}</span><h2>{{ __('studio.contact') }}</h2>
        </div>
        <div class="contact-form">
            @include('form')
        </div>
    </div>
</section>
@endsection
