@extends('admin.shell')
@section('title','İçerik önizlemesi')
@section('body')<article lang="{{ $translation->locale }}"><h2>{{ $translation->title }}</h2><p>{{ $translation->excerpt }}</p>{!! $translation->body !!}</article>@endsection
