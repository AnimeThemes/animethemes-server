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

<form action="{{ route('resource.update', $resource->resource_id) }}" method="POST">
    @csrf
    @method('PUT')
    <div>
        <strong>Type:</strong>
        <select name="type">
        @foreach ($resourceTypes as $value => $description)
            <option value="{{ $value }}" {{ $resource->type->value === $value ? 'selected' : ''}}>{{ $description }}</option>
        @endforeach
        </select>
    </div>
    <div>
        <strong>Link:</strong>
        <input type="text" name="link" placeholder="Link" value="{{ $resource->link }}">
    </div>
    <div>
        <strong>Label:</strong>
        <input type="text" name="label" placeholder="Label" value="{{ $resource->label }}">
    </div>
    <div>
        <button type="submit">Submit</button>
    </div>
</form>

@endsection