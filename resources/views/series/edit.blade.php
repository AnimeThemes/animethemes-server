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

<form action="{{ route('series.update', $series->alias) }}" method="POST">
    @csrf
    @method('PUT')
    <div>
        <strong>Alias:</strong>
        <input type="text" name="alias" placeholder="Alias" value="{{ $series->alias }}">
    </div>
    <div>
        <strong>Name:</strong>
        <input type="text" name="name" placeholder="Name" value="{{ $series->name }}">
    </div>
    <div>
        <select name="anime[]" multiple>
        @foreach ($anime as $theAnime)
            <option value="{{ $theAnime->anime_id }}" {{ $series->anime->contains($theAnime->anime_id) ? 'selected' : '' }}>{{ $theAnime->name }}</option>
        @endforeach
        </select>
    </div>
    <div>
        <button type="submit">Submit</button>
    </div>
</form>

@endsection