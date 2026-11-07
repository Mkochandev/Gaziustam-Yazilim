<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title') · GaziUstam</title>
    <link rel="stylesheet" href="/team.css">
    @stack('head')
</head>
<body class="team-layout">
    <nav class="team-navigation" aria-label="Yönetim">
        <a href="/admin"><strong>gaziustam</strong></a>
        <a href="/admin">Genel bakış</a>
        @can('content')
            <a href="/admin/contents">Haber ve portföy</a>
            <a href="/admin/categories">Kategoriler</a>
            <a href="/admin/media">Medya</a>
        @endcan
        @can('sales')<a href="/admin/talepler">Talepler</a>@endcan
        @can('manage')
            <a href="/admin/form">Teklif formu</a>
            <a href="/admin/locales">Diller</a>
            <a href="/admin/erisim">API ve kullanıcılar</a>
            <a href="/admin/ayarlar">Ayarlar</a>
        @endcan
        @auth
            <small>{{ auth()->user()->name }}</small>
            <form method="post" action="{{ route('filament.admin.auth.logout') }}">@csrf<button>Çıkış yap</button></form>
        @endauth
    </nav>
    <main>
        <h1>@yield('title')</h1>
        @if(session('notice'))<p role="status">{{ session('notice') }}</p>@endif
        @if($errors->any())<ul role="alert">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
        @yield('body')
    </main>
    @stack('scripts')
</body>
</html>

