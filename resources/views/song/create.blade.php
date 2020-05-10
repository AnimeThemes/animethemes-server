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

<form action="{{ route('song.store') }}" method="POST">
    @csrf
    <div>
        <strong>Title:</strong>
        <input type="text" name="title" placeholder="Title">
    </div>
    <div>
        <strong>By:</strong>
        <input type="text" name="by" placeholder="By">
    </div>
    <div>
        <strong>Artists:</strong>
        <select name="artists[]" multiple>
        @foreach ($artists as $artist)
            <option value="{{ $artist->artist_id }}">{{ $artist->name }}</option>
        @endforeach
        </select>
    </div>
    <div>
        <button type="submit">Submit</button>
    </div>
</form>

@endsection