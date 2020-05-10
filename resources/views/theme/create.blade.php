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

<form action="{{ route('anime.theme.store', $anime_alias) }}" method="POST">
    @csrf
    <div>
        <strong>Type:</strong>
        <select name="type">
        @foreach ($themeTypes as $value => $description)
            <option value="{{ $value }}">{{ $description }}</option>
        @endforeach
        </select>
    </div>
    <div>
        <strong>Sequence:</strong>
        <input type="number" name="sequence">
    </div>
    <div>
        <strong>Song:</strong>
        <select name="song">
        @foreach ($songs as $song)
            <option value="{{ $song->song_id }}">{{ $song->title }}</option>
        @endforeach
        </select>
    </div>
    <div>
        <strong>Group:</strong>
        <input type="text" name="group" placeholder="Group">
    </div>
    <div>
        <button type="submit">Submit</button>
    </div>
</form>

@endsection