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
    @foreach ($artist->externalResources as $resource)
        <p>{{ $resource->link }}</p>
    @endforeach
</div>
<div>
    <strong>Songs:</strong>
    @foreach ($artist->songs as $song)
        <p>{{ $song->title }}</p>
    @endforeach
</div>

@endsection