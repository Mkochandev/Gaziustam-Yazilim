@extends('admin.shell')
@section('title',$item->public_reference)
@section('body')
<div class="columns"><section><h2>{{ $item->contact_name }}</h2><p>{{ $item->contact_email }} · {{ $item->locale }}</p>
@php($snapshot=json_decode($item->schema_snapshot,true))
@foreach($answers as $a)@php($value=json_decode($a->answer,true))<h3>{{ $snapshot[$a->field_key]['label'] }}</h3><p>{{ collect(is_array($value)?$value:[$value])->map(fn($v)=>$snapshot[$a->field_key]['options'][$v]??$v)->implode(', ') }}</p>@endforeach
@foreach($attachments as $file)<p><a href="/admin/talepler/{{ $item->id }}/ek/{{ $file->id }}">{{ $file->original_name }}</a></p>@endforeach</section>
<section><form method="post">@csrf<label>Durum<select name="status">@foreach($states as $key=>$label)<option value="{{ $key }}" @selected($item->status===$key)>{{ $label }}</option>@endforeach</select></label>
<label>Sorumlu<select name="assigned_to"><option value="">Atanmadı</option>@foreach($team as $u)<option value="{{ $u->id }}" @selected($item->assigned_to==$u->id)>{{ $u->name }}</option>@endforeach</select></label>
<label>İç not<textarea name="note">{{ old('note') }}</textarea></label><button>Kaydet</button></form><h2>Geçmiş</h2>
@foreach($events as $event)<p>{{ $event->created_at }} · {{ $event->old_status?$states[$event->old_status].' → ':'' }}{{ $states[$event->new_status] }} · {{ $event->note }}</p>@endforeach</section></div>
@endsection
