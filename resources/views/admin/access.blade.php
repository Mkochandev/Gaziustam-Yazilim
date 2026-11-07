@extends('admin.shell')
@section('title','API erişimleri ve kullanıcılar')
@section('body')
@if(session('token'))<section>Tokenı şimdi kopyalayın: <code>{{ session('token') }}</code></section>@endif
<div class="columns"><section><h2>API tokenları</h2><form method="post" action="/admin/tokens">@csrf<label>Token adı<input required name="name"></label>@foreach(['read'=>'Oku','write'=>'Yaz','publish'=>'Yayınla'] as $key=>$label)<label><input type="checkbox" name="abilities[]" value="posts:{{ $key }}"> {{ $label }}</label>@endforeach<button>Token oluştur</button></form>
@foreach($tokens as $token)<form method="post" action="/admin/tokens/{{ $token->id }}">@csrf @method('DELETE'){{ $token->name }} · Son kullanım {{ $token->last_used_at??'—' }} <button>İptal et</button></form>@endforeach</section>
<section><h2>Kullanıcılar</h2>@foreach($users as $u)
<form method="post" action="/admin/users/{{ $u->id }}">@csrf @method('PATCH')
<strong>{{ $u->name }}</strong><label>Rol<select name="role">@foreach(['owner','editor','sales'] as $role)<option @selected($u->role===$role)>{{ $role }}</option>@endforeach</select></label>
<label>Hesap<select name="active"><option value="1" @selected($u->active)>Etkin</option><option value="0" @selected(!$u->active)>Kapalı</option></select></label><button>Yetkiyi güncelle</button>
</form>@endforeach
<form method="post" action="/admin/users">@csrf<label>Ad<input required name="name"></label><label>E-posta<input required type="email" name="email"></label><label>Parola<input required type="password" minlength="12" autocomplete="new-password" name="password"></label><label>Rol<select name="role">@foreach(['editor','sales','owner'] as $role)<option>{{ $role }}</option>@endforeach</select></label><button>Kullanıcı oluştur</button></form></section></div>
@endsection
