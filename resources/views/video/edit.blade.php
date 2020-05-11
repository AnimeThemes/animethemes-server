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

<form action="{{ route('video.update', $video->basename) }}" method="POST">
    @csrf
    @method('PUT')
    <div>
        <strong>Resolution:</strong>
        <input type="integer" name="resolution" placeholder="Resolution" value="{{ $video->resolution }}">
    </div>
    <div>
        <strong>NC:</strong>
        <input type="checkbox" name="nc" {{ $video->nc === 1 ? 'checked' : '' }}>
    </div>
    <div>
        <strong>Subbed:</strong>
        <input type="checkbox" name="subbed" {{ $video->subbed === 1 ? 'checked' : '' }}>
    </div>
    <div>
        <strong>Lyrics:</strong>
        <input type="checkbox" name="lyrics" {{ $video->lyrics === 1 ? 'checked' : '' }}>
    </div>
    <div>
        <strong>Uncensored:</strong>
        <input type="checkbox" name="uncen" {{ $video->uncen === 1 ? 'checked' : '' }}>
    </div>
    <div>
        <strong>Trans:</strong>
        <input type="checkbox" name="trans" {{ $video->trans === 1 ? 'checked' : '' }}>
    </div>
    <div>
        <strong>Over:</strong>
        <input type="checkbox" name="over" {{ $video->over === 1 ? 'checked' : '' }}>
    </div>
    <div>
        <strong>Source:</strong>
        <select name="season">
        @foreach ($sourceTypes as $value => $description)
            <option value="{{ $value }}" {{ !empty($video->source) && $video->source->value === $value ? 'selected' : ''}}>{{ $description }}</option>
        @endforeach
        </select>
    </div>
    <div>
        <button type="submit">Submit</button>
    </div>
</form>

@endsection