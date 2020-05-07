@extends('layouts.app')

@section('content')

@foreach ($anime as $theAnime)
    <p><a href="{{ route('anime.show', $theAnime->alias) }}">{{ $theAnime->name }}</a></p>
@endforeach

@endsection