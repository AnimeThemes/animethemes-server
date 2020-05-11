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

<form action="{{ route('anime.theme.update', [$anime_alias, $theme->slug]) }}" method="POST">
    @csrf
    @method('PUT')
    <div>
        <strong>Type:</strong>
        <select name="type">
        @foreach ($themeTypes as $value => $description)
            <option value="{{ $value }}" {{ $theme->type->value === $value ? 'selected' : ''}}>{{ $description }}</option>
        @endforeach
        </select>
    </div>
    <div>
        <strong>Sequence:</strong>
        <input type="number" name="sequence" value="{{ $theme->sequence }}">
    </div>
    <div>
        <strong>Song:</strong>
        <select name="song">
        @foreach ($songs as $song)
            <option value="{{ $song->song_id }}" {{ !empty($theme->song) && $theme->song->song_id === $song->song_id ? 'selected' : '' }}>{{ $song->title }}</option>
        @endforeach
        </select>
    </div>
    <div>
        <strong>Group:</strong>
        <input type="text" name="group" placeholder="Group" value="{{ $theme->group }}">
    </div>
    <div>
        <button type="submit">Submit</button>
    </div>
</form>

@endsection