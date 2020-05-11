@extends('layouts.app')

@section('content')

@foreach ($entries as $entry)
    <p><a href="{{ route('anime.theme.entry.show', [$anime_alias, $theme_slug, $entry->entry_id]) }}">{{ $entry->entry_id }}</a></p>
@endforeach

@endsection