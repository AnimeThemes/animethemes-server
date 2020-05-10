@extends('layouts.app')

@section('content')

<div>
    <strong>Text:</strong>
    {{$synonym->text}}
</div>
<div>
    <strong>Anime:</strong>
    <a href="{{ route('anime.show', $synonym->anime->alias) }}">{{ $synonym->anime->name }}</a>
</div>

@endsection