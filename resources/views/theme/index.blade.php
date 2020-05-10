@extends('layouts.app')

@section('content')

@foreach ($themes as $theme)
    <p><a href="{{ route('anime.theme.show', [$anime_alias, $theme->slug]) }}">{{ $theme->slug }}</a></p>
@endforeach

@endsection