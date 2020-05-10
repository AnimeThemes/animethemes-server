@extends('layouts.app')

@section('content')

<div>
    <strong>Alias:</strong>
    {{$series->alias}}
</div>
<div>
    <strong>Name:</strong>
    {{$series->name}}
</div>
<div>
    <strong>Anime:</strong>
    @foreach ($series->anime as $anime)
        <p><a href="{{ route('anime.show', $anime->alias) }}">{{ $anime->name }}</a></p>
    @endforeach
</div>

@endsection