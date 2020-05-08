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

<form action="{{ route('anime.synonym.update', [$anime_alias, $synonym->synonym_id]) }}" method="POST">
    @csrf
    @method('PUT')
    <div>
        <strong>Text:</strong>
        <input type="text" name="text" placeholder="Text" value="{{ $synonym->text }}">
    </div>
    <div>
        <button type="submit">Submit</button>
    </div>
</form>

@endsection