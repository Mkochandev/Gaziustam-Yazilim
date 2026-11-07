@extends('admin.shell')
@section('title','Ayarlar ve işletim')
@section('body')
<section><h2>Bildirim kuyruğu</h2><p>{{ $pending }} bekleyen iş</p><p>Mail sürücüsü {{ config('mail.default') }} · Bildirim alıcısı {{ config('studio.notify')?'tanımlı':'eksik' }}</p><p>Talep saklama süresi {{ config('studio.retention_days') }} gün. Değişiklikler sunucunun ortam yapılandırmasından uygulanır.</p>
@forelse($failed as $job)<form action="/admin/ayarlar/retry/{{ $job->uuid }}" method="post">@csrf {{ $job->failed_at }} <button>Tekrar dene</button></form>@empty<p>Başarısız iş yok.</p>@endforelse
{{ $failed->links('pagination::simple-default') }}</section>
<section><h2>Son işlemler</h2>@foreach($logs as $log)<p>{{ $log->created_at }} · Kullanıcı {{ $log->actor_id??'sistem' }} · {{ $log->action }} · {{ $log->subject_type }} #{{ $log->subject_id }}</p>@endforeach</section>
@endsection
