@extends('layouts.app')

@section('content')

<div>
    <strong>Version:</strong>
    {{ $entry->version }}
</div>
<div>
    <strong>Episodes:</strong>
    {{ $entry->episodes }}
</div>
<div>
    <strong>NSFW:</strong>
    {{ $entry->nsfw }}
</div>
<div>
    <strong>Spoiler:</strong>
    {{ $entry->spoiler }}
</div>
<div>
    <strong>SFX:</strong>
    {{ $entry->sfx }}
</div>
<div>
    <strong>Notes:</strong>
    {{ $entry->notes }}
</div>
<div>
    <strong>Videos:</strong>
    @foreach ($entry->videos as $video)
        <p><a href="{{ route('video.show', $video->basename) }}">{{ $video->filename }}</a></p>
    @endforeach
</div>

@endsection