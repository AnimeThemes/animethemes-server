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

@endsection