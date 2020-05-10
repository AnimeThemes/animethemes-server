@extends('layouts.app')

@section('content')

<div>
    <strong>Alias:</strong>
    {{$artist->alias}}
</div>
<div>
    <strong>Name:</strong>
    {{$artist->name}}
</div>
<div>
    <strong>Resources:</strong>
    @foreach ($artist->resources as $resource)
        <p><a href="{{ route('resource.show', $resource->resource_id) }}">{{ $resource->link }}</a></p>
    @endforeach
</div>
<div>
    <strong>Songs:</strong>
    @foreach ($artist->songs as $song)
        <p><a href="{{ route('song.show', $song->song_id) }}">{{ $song->title }}</a></p>
    @endforeach
</div>

@endsection