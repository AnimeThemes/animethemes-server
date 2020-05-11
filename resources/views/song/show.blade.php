@extends('layouts.app')

@section('content')

<div>
    <strong>Title:</strong>
    {{$song->title}}
</div>
<div>
    <strong>By:</strong>
    {{$song->by}}
</div>
<div>
    <strong>Artists:</strong>
    @foreach ($song->artists as $artist)
        <p><a href="{{ route('artist.show', $artist->alias) }}">{{ $artist->name }}</a></p>
    @endforeach
</div>
<div>
    <strong>Themes:</strong>
    @foreach ($song->themes as $theme)
        <p><a href="{{ route('anime.theme.show', [$theme->anime->alias, $theme->slug]) }}">{{ $theme->slug }}</a></p>
    @endforeach
</div>

@endsection