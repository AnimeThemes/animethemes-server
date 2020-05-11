@extends('layouts.app')

@section('content')

<div>
    <strong>Type:</strong>
    {{ $theme->type->key }}
</div>
<div>
    <strong>Sequence:</strong>
    {{ $theme->sequence }}
</div>
<div>
    <strong>Song:</strong>
    @if (!empty($theme->song))
    <a href="{{ route('song.show', $theme->song->song_id) }}">{{ $theme->song->title }}</a>
    @endif
</div>
<div>
    <strong>Group:</strong>
    {{ $theme->group }}
</div>
<div>
    <strong>Anime:</strong>
    <a href="{{ route('anime.show', $theme->anime->alias) }}">{{ $theme->anime->name }}</a>
</div>

@endsection