<!doctype html>
<html lang="{{ $locale }}" dir="{{ collect($locales)->firstWhere('code', $locale)->direction ?? 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'GaziUstam — '.__('studio.headline').' '.__('studio.accent'))</title>
    @if(is_file(public_path('build/manifest.json'))) @vite('resources/css/app.css') @else <link rel="stylesheet" href="{{ asset('studio.css') }}"> @endif
    @php
        $listingPath ??= request()->segment(2) ?? '';
        $localizedUrls = $alternates ?? collect($locales)->mapWithKeys(fn ($language) => [
            $language->code => url(trim($language->code.'/'.$listingPath, '/')),
        ])->all();
    @endphp
    <link rel="canonical" href="{{ url()->current() }}">
    @if(request()->query())<meta name="robots" content="noindex,follow">@endif
    @foreach($localizedUrls as $code => $url)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ $url }}">
    @endforeach
    @yield('head')
    @stack('head')
</head>
<body id="top">
<a href="#main" class="skip-link">{{ __('studio.skip') }}</a>
<header class="studio-header">
    <div class="wrap">
        <nav aria-label="{{ __('studio.menu') }}">
            <a class="logo" href="{{ url($locale) }}"><span class="studio-mark"><x-studio-star /></span>gaziustam</a>
            <div class="navlinks">
                <a href="{{ url($locale).'#leistungen' }}">{{ __('studio.services') }}</a>
                <a href="{{ url($locale.'/projects') }}">{{ __('studio.projects') }}</a>
                <a href="{{ url($locale.'/blog') }}">{{ __('studio.journal') }}</a>
            </div>
            <details class="language-picker">
                <summary aria-label="{{ __('studio.language') }} · {{ strtoupper($locale) }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/><ellipse cx="12" cy="12" rx="4" ry="9"/><path d="M3 12h18"/>
                    </svg>
                    {{ strtoupper($locale) }} <span aria-hidden="true">⌄</span>
                </summary>
                <div class="language-menu">
                    @foreach($locales as $language)
                        @php
                            $target = $localizedUrls[$language->code] ?? url(trim($language->code.'/'.$listingPath, '/'));
                            $missing = isset($alternates) && !isset($alternates[$language->code]);
                        @endphp
                        <a href="{{ $target }}" hreflang="{{ $language->code }}" @if($locale === $language->code) aria-current="page" @endif>
                            <span lang="{{ $language->code }}">{{ $language->native_name }}</span>
                            @if($missing)<small>{{ __('studio.unavailable') }}</small>@endif
                        </a>
                    @endforeach
                </div>
            </details>
            <a class="btn nav-cta" href="{{ url($locale).'#kontakt' }}">{{ __('studio.cta') }}<span aria-hidden="true">↗</span></a>
            <details class="mobile-details">
                <summary aria-label="{{ __('studio.menu') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M2 6h20M2 12h20M2 18h20"/></svg>
                </summary>
                <div class="mobile-menu">
                    <a href="{{ url($locale).'#leistungen' }}">{{ __('studio.services') }}</a>
                    <a href="{{ url($locale.'/projects') }}">{{ __('studio.projects') }}</a>
                    <a href="{{ url($locale.'/blog') }}">{{ __('studio.journal') }}</a>
                    <a href="{{ url($locale).'#kontakt' }}">{{ __('studio.cta') }} ↗</a>
                </div>
            </details>
        </nav>
    </div>
</header>
<main id="main">
    @yield('content')
</main>
<footer class="studio-footer">
    <div class="wrap">
        <div class="footer-topline">
            <span>TÜRKİYE → EUROPE / DIGITAL STUDIO</span>
            <div>
                <a href="{{ url($locale.'/projects') }}">{{ __('studio.projects') }}</a>
                <a href="{{ url($locale.'/blog') }}">{{ __('studio.journal') }}</a>
                <a href="{{ url($locale.'/privacy') }}">{{ __('studio.privacy') }}</a>
                <a href="{{ url($locale.'/legal') }}">{{ __('studio.legal') }}</a>
            </div>
        </div>
        <a class="footer-wordmark" href="{{ url($locale) }}">gaziustam<span><x-studio-star /></span></a>
        <div class="footer-bottomline">
            <span>SOFTWARE. DESIGN. AUTOMATION.</span>
            <span>DE / AT / EUROPE</span>
            <a href="#top" aria-label="{{ __('studio.top') }}">↑</a>
        </div>
    </div>
</footer>
<script>
    document.querySelectorAll('header details').forEach((menu) => {
        document.addEventListener('click', (event) => {
            if (!menu.contains(event.target) || event.target.closest('a')) menu.removeAttribute('open');
        });
        menu.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && menu.open) {
                menu.open = false;
                menu.querySelector('summary').focus();
            }
        });
    });
</script>
</body>
</html>

