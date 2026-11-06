@extends('admin.shell')
@section('title','Teklif formu')
@section('body')
<nav>@foreach($versions as $v)<a href="?v={{ $v->id }}">v{{ $v->version_number }} · {{ ['draft'=>'taslak','published'=>'yayında','retired'=>'emekli'][$v->status] }}</a>@endforeach</nav>
<div class="columns"><div><form method="post" action="/admin/form" id="schema-editor" data-dirty>@csrf
<h2>Alanlar</h2><p>Mevcut bir alanın anahtarı ve türü korunur. Düzenlemeler yeni taslak sürümüne kaydedilir.</p>
<div id="fields"></div><button id="add-field" type="button" hidden>Alan ekle</button>
<details><summary>Şema (JSON)</summary><textarea name="schema" id="schema" rows="16">{{ old('schema',json_encode(json_decode($version->schema),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)) }}</textarea></details>
<button>Taslaktan yeni sürüm kaydet</button></form>
@if($version->status==='draft')<form method="post" action="/admin/form/{{ $version->id }}/publish">@csrf<button>Bu sürümü yayınla</button></form>@endif
</div><section><h2>Kaydedilmiş sürümün önizlemesi</h2><nav>@foreach(['de','en','tr'] as $l)<a href="?v={{ $version->id }}&lang={{ $l }}">{{ strtoupper($l) }}</a>@endforeach</nav>
@include('form',['preview'=>true])
</section></div><script src="/form-editor.js" defer></script><script src="/dirty.js" defer></script>
@endsection
