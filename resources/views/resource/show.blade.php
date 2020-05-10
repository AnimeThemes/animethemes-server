@extends('layouts.app')

@section('content')

<div>
    <strong>Type:</strong>
    {{ $resource->type->description }}
</div>
<div>
    <strong>Link:</strong>
    {{ $resource->link }}
</div>
<div>
    <strong>Label:</strong>
    {{ $resource->label }}
</div>
<div>
    <strong>Anime:</strong>
    @foreach ($resource->anime as $anime)
        <p><a href="{{ route('anime.show', $anime->alias) }}">{{ $anime->name }}</a></p>
    @endforeach
</div>
<div>
    <strong>Artist:</strong>
    @foreach ($resource->artists as $artist)
        <p><a href="{{ route('artist.show', $artist->alias) }}">{{ $artist->name }}</a></p>
    @endforeach
</div>

@endsection