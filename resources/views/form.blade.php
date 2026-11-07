@php $schema=json_decode($version->schema,true); @endphp
@if(session('reference'))<p role="status">{{ __('forms.saved') }} <strong>{{ session('reference') }}</strong></p>@endif
@if($errors->any())<div role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
@if($errors->has('version_id'))
<details open><summary>{{ __('forms.recovered') }}</summary><pre style="white-space:pre-wrap;overflow-wrap:anywhere">{{ json_encode(old('answers',[]),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></details>
@endif
<form class="enquiry-fields" method="post" enctype="multipart/form-data" action="{{ url("/$locale/enquiries") }}">
@csrf
<input type="hidden" name="version_id" value="{{ $version->id }}">
<input type="hidden" name="idempotency_key" value="{{ old('idempotency_key', (string) Str::uuid()) }}">
<div hidden><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
@foreach($schema as $f)
@continue(!$f['active'])
@php
$key=$f['key']; $type=$f['type']; $id='field-'.$key;
$name="answers[$key]"; $errorKey="answers.$key"; $value=old($errorKey,$key==='service'?request('service'):null);
@endphp
<div class="field {{ $f['width']==='full'?'wide':'' }}">
<label for="{{ $id }}">{{ $f['labels'][$locale] }} {{ $f['required']?'*':'' }}</label>
@if($type==='textarea')
<textarea id="{{ $id }}" name="{{ $name }}" @required($f['required']) aria-describedby="{{ $id }}-hint" aria-invalid="{{ $errors->has($errorKey) || $errors->has($errorKey.'.*') ? 'true' : 'false' }}" placeholder="{{ $f['placeholder'][$locale]??'' }}">{{ is_string($value)?$value:'' }}</textarea>
@elseif($type==='select' || $type==='multi')
<select id="{{ $id }}" name="{{ $name }}{{ $type==='multi'?'[]':'' }}" @if($type==='multi') multiple @endif @required($f['required']) aria-describedby="{{ $id }}-hint" aria-invalid="{{ $errors->has($errorKey) || $errors->has($errorKey.'.*') ? 'true' : 'false' }}">
@if($type==='select')<option value="">—</option>@endif
@foreach($f['options'] as $opt)<option value="{{ $opt['value'] }}" @selected(in_array($opt['value'],(array)$value,true))>{{ $opt['labels'][$locale] }}</option>@endforeach
</select>
@elseif($type==='radio')
@foreach($f['options'] as $opt)<label><input type="radio" id="{{ $loop->first?$id:$id.'-'.$loop->index }}" name="{{ $name }}" value="{{ $opt['value'] }}" @checked($value===$opt['value']) @required($f['required']) aria-describedby="{{ $id }}-hint" aria-invalid="{{ $errors->has($errorKey) || $errors->has($errorKey.'.*') ? 'true' : 'false' }}"> {{ $opt['labels'][$locale] }}</label>@endforeach
@elseif($type==='file')
<input type="file" multiple id="{{ $id }}" name="{{ $name }}[]" accept=".pdf,.jpg,.jpeg,.png,.webp" aria-describedby="{{ $id }}-hint" aria-invalid="{{ $errors->has($errorKey) || $errors->has($errorKey.'.*') ? 'true' : 'false' }}" @required($f['required'])>
@elseif($type==='checkbox')
<input type="checkbox" id="{{ $id }}" name="{{ $name }}" value="1" @checked($value) @required($f['required']) aria-describedby="{{ $id }}-hint" aria-invalid="{{ $errors->has($errorKey) || $errors->has($errorKey.'.*') ? 'true' : 'false' }}">
@else
<input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ is_scalar($value)?$value:'' }}" @required($f['required']) placeholder="{{ $f['placeholder'][$locale]??'' }}" aria-describedby="{{ $id }}-hint" aria-invalid="{{ $errors->has($errorKey) || $errors->has($errorKey.'.*') ? 'true' : 'false' }}">
@endif
<small id="{{ $id }}-hint">{{ $f['help'][$locale]??'' }} {{ $errors->first($errorKey) }} {{ $errors->first($errorKey.'.*') }}</small>
</div>
@endforeach
@if(config('studio.turnstile_key'))
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script><div class="cf-turnstile" data-sitekey="{{ config('studio.turnstile_key') }}"></div>
@endif
@if(empty($preview))<button class="btn" type="submit">{{ __('forms.send') }} ↗</button>@endif
</form>


