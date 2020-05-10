@extends('layouts.app')

@section('content')

<div>
    <strong>Alias:</strong>
    {{$anime->alias}}
</div>
<div>
    <strong>Name:</strong>
    {{$anime->name}}
</div>
<div>
    <strong>Year:</strong>
    {{ $anime->year }}
</div>
<div>
    <strong>Season:</strong>
    {{ $anime->season->description }}
</div>
<div>
    <strong>Synonyms:</strong>
    @foreach ($anime->synonyms as $synonym)
        <p><a href="{{ route('anime.synonym.show', [$anime->alias, $synonym->synonym_id]) }}">{{ $synonym->text }}</a></p>
    @endforeach
</div>
<div>
    <strong>Series:</strong>
    @foreach ($anime->series as $series)
        <p><a href="{{ route('series.show', $series->alias) }}">{{ $series->name }}</a></p>
    @endforeach
</div>
<div>
    <strong>Resources:</strong>
    @foreach ($anime->resources as $resource)
        <p><a href="{{ route('resource.show', $resource->resource_id) }}">{{ $resource->link }}</a></p>
    @endforeach
</div>

@endsection