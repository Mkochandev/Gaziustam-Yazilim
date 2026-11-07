@extends('admin.shell')
@section('title','Dil sürümü · '.strtoupper($locale))
@push('head')
<link rel="stylesheet" href="{{ asset('css/filament/filament/app.css') }}">
@filamentStyles
@livewireStyles
@endpush
@push('scripts')
@filamentScripts
@livewireScripts
@endpush
@section('body')
<nav>@foreach($editingLocales as $language)<a href="/admin/yazilar/{{ $content->id }}/{{ $language->code }}" @if($language->code===$locale) aria-current="page" @endif>{{ $language->native_name }}</a>@endforeach
@if($translation->id)<a href="{{ URL::temporarySignedRoute('studio.preview',now()->addMinutes(20),['translation'=>$translation]) }}">Kaydedilmiş sürümü önizle</a>@endif</nav>
<details><summary>Dil sürümü ekle</summary><nav>@foreach(\Illuminate\Support\Facades\DB::table('locales')->orderBy('sort_order')->get() as $language)<a href="/admin/yazilar/{{ $content->id }}/{{ $language->code }}">{{ $language->native_name }}</a>@endforeach</nav></details>
@livewire('translation-editor', ['contentId' => $content->id, 'locale' => $locale])
<details><summary>HTML ile düzenle ve kaydedilmiş sürümü önizle</summary>
<form method="post" data-dirty>@csrf
@foreach(['title'=>'Başlık','slug'=>'Adres','excerpt'=>'Özet','body'=>'Gövde (HTML)','cover_alt'=>'Kapak alt metni','meta_title'=>'SEO başlığı','meta_description'=>'SEO açıklaması']+($content->type==='project'?['problem'=>'Sorun','solution'=>'Çözüm','deliverables'=>'Teslimatlar']:[]) as $key=>$label)
<label>{{ $label }}@if(in_array($key,['body','excerpt','problem','solution','deliverables']))<textarea name="{{ $key }}" rows="{{ $key==='body'?12:3 }}">{{ old($key,$translation->$key) }}</textarea>@else<input name="{{ $key }}" value="{{ old($key,$translation->$key) }}" @required(in_array($key,['title','slug']))>@endif</label>
@endforeach<label>Durum<select name="status"><option value="draft">Taslak</option><option value="published" @selected(old('status',$translation->status)==='published')>Yayında / planlı</option></select></label>
<label>Yayın tarihi (UTC)<input type="datetime-local" name="published_at" value="{{ old('published_at',$translation->published_at?->format('Y-m-d\TH:i')) }}"></label><button>Kaydet</button>
@if($translation->id)<a href="{{ URL::temporarySignedRoute('studio.preview',now()->addMinutes(20),['translation'=>$translation]) }}">Kaydedilmiş sürümü önizle</a>@endif
</form><script src="/dirty.js" defer></script>
</details>@endsection

