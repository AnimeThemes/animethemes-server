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

<form action="{{ route('artist.update', $artist->alias) }}" method="POST">
    @csrf
    @method('PUT')
    <div>
        <strong>Alias:</strong>
        <input type="text" name="alias" placeholder="Alias" value="{{ $artist->alias }}">
    </div>
    <div>
        <strong>Name:</strong>
        <input type="text" name="name" placeholder="Name" value="{{ $artist->name }}">
    </div>
    <div>
        <strong>Resources:</strong>
        <select name="resources[]" multiple>
        @foreach ($resources as $resource)
            <option value="{{ $resource->resource_id }}" {{ $artist->externalResources->contains($resource->resource_id) ? 'selected' : '' }}>{{ $resource->link }}</option>
        @endforeach
        </select>
    </div>
    <div>
        <button type="submit">Submit</button>
    </div>
</form>

@endsection