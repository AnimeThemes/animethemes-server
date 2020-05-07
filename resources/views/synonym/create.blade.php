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

<form action="{{ route('anime.synonym.store', $anime_alias) }}" method="POST">
    @csrf
    <div>
        <strong>Text:</strong>
        <input type="text" name="text" placeholder="Text">
    </div>
    <div>
        <button type="submit">Submit</button>
    </div>
</form>

@endsection