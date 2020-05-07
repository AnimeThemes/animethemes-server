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

<form action="{{ route('anime.store') }}" method="POST">
    @csrf
    <div>
        <strong>Alias:</strong>
        <input type="text" name="alias" placeholder="Alias">
    </div>
    <div>
        <strong>Name:</strong>
        <input type="text" name="name" placeholder="Name">
    </div>
    <div>
        <strong>Year:</strong>
        <input type="number" name="year">
    </div>
    <div>
        <strong>Season:</strong>
        <select name="season">
        @foreach ($seasons as $value => $description)
            <option value="{{ $value }}">{{ $description }}</option>
        @endforeach
        </select>
    </div>
    <div>
        <button type="submit">Submit</button>
    </div>
</form>

@endsection