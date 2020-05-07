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

<form action="{{ route('anime.update', $anime->alias) }}" method="POST">
    @csrf
    @method('PUT')
    <div>
        <strong>Alias:</strong>
        <input type="text" name="alias" placeholder="Alias" value="{{ $anime->alias }}">
    </div>
    <div>
        <strong>Name:</strong>
        <input type="text" name="name" placeholder="Name" value="{{ $anime->name }}">
    </div>
    <div>
        <strong>Year:</strong>
        <input type="number" name="year" value="{{ $anime->year }}">
    </div>
    <div>
        <strong>Season:</strong>
        <select name="season">
        @foreach ($seasons as $value => $description)
            <option value="{{ $value }}" {{ $anime->season->value === $value ? 'selected' : ''}}>{{ $description }}</option>
        @endforeach
        </select>
    </div>
    <div>
        <button type="submit">Submit</button>
    </div>
</form>

@endsection