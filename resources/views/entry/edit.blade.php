@extends('layouts.app')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('anime.theme.entry.update', [$anime_alias, $theme_slug, $entry->entry_id]) }}" method="POST">
    @csrf
    @method('PUT')
    <div>
        <strong>Version:</strong>
        <input type="integer" name="version" placeholder="Version" value="{{ $entry->version }}">
    </div>
    <div>
        <strong>Episodes:</strong>
        <input type="text" name="episodes" placeholder="Episodes" value="{{ $entry->episodes }}">
    </div>
    <div>
        <strong>NSFW:</strong>
        <input type="checkbox" name="nsfw" {{ $entry->nsfw === 1 ? 'checked' : '' }}>
    </div>
    <div>
        <strong>Spoiler:</strong>
        <input type="checkbox" name="spoiler" {{ $entry->spoiler === 1 ? 'checked' : '' }}>
    </div>
    <div>
        <strong>SFX:</strong>
        <input type="checkbox" name="sfx" {{ $entry->sfx === 1 ? 'checked' : '' }}>
    </div>
    <div>
        <strong>Notes:</strong>
        <input type="text" name="notes" placeholder="Notes" value="{{ $entry->notes }}">
    </div>
    <div>
        <strong>Videos:</strong>
        <input type="text" name="videos" placeholder="Videos">
    </div>
    <div>
        <button type="submit">Submit</button>
    </div>
</form>

@endsection