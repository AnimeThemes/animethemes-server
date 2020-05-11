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

<form action="{{ route('anime.theme.entry.store', [$anime_alias, $theme_slug]) }}" method="POST">
    @csrf
    <div>
        <strong>Version:</strong>
        <input type="integer" name="version" placeholder="Version">
    </div>
    <div>
        <strong>Episodes:</strong>
        <input type="text" name="episodes" placeholder="Episodes">
    </div>
    <div>
        <strong>NSFW:</strong>
        <input type="checkbox" name="nsfw">
    </div>
    <div>
        <strong>Spoiler:</strong>
        <input type="checkbox" name="spoiler">
    </div>
    <div>
        <strong>SFX:</strong>
        <input type="checkbox" name="sfx">
    </div>
    <div>
        <strong>Notes:</strong>
        <input type="text" name="notes" placeholder="Notes">
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