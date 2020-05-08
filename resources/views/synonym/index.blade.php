@extends('layouts.app')

@section('content')

@foreach ($synonyms as $synonym)
    <p><a href="{{ route('anime.synonym.show', [$anime_alias, $synonym->synonym_id]) }}">{{ $synonym->text }}</a></p>
@endforeach

@endsection