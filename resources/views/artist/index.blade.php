@extends('layouts.app')

@section('content')

@foreach ($artists as $artist)
    <p><a href="{{ route('artist.show', $artist->alias) }}">{{ $artist->name }}</a></p>
@endforeach

@endsection