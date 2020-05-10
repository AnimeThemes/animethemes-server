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

<form action="{{ route('song.update', $song->song_id) }}" method="POST">
    @csrf
    @method('PUT')
    <div>
        <strong>Title:</strong>
        <input type="text" name="title" placeholder="Title" value="{{ $song->title }}">
    </div>
    <div>
        <strong>By:</strong>
        <input type="text" name="by" placeholder="By" value="{{ $song->by }}">
    </div>
    <div>
        <strong>Artists:</strong>
        <select name="artists[]" multiple>
        @foreach ($artists as $artist)
            <option value="{{ $artist->artist_id }}" {{ $song->artists->contains($artist->artist_id) ? 'selected' : '' }}>{{ $artist->name }}</option>
        @endforeach
        </select>
    </div>
    <div>
        <button type="submit">Submit</button>
    </div>
</form>

@endsection