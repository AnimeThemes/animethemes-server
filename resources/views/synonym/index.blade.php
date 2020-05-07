@extends('layouts.app')

@section('content')

@foreach ($synonyms as $synonym)
    <p><a href="{{ route('anime.synonym.show', $synonym->synonym_id) }}">{{ $synonym->name }}</a></p>
@endforeach

@endsection