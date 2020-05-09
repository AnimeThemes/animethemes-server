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

<form action="{{ route('resource.store') }}" method="POST">
    @csrf
    <div>
        <strong>Type:</strong>
        <select name="type">
        @foreach ($resourceTypes as $value => $description)
            <option value="{{ $value }}">{{ $description }}</option>
        @endforeach
        </select>
    </div>
    <div>
        <strong>Link:</strong>
        <input type="text" name="link" placeholder="Link">
    </div>
    <div>
        <strong>Label:</strong>
        <input type="text" name="label" placeholder="Label">
    </div>
    <div>
        <button type="submit">Submit</button>
    </div>
</form>

@endsection