@extends('layouts.app')

@section('content')

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Type</th>
            <th>Link</th>
            <th>Label</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($resources as $resource)
        <tr>
            <td><a href="{{ route('resource.show', $resource->resource_id) }}">{{ $resource->resource_id }}</a></td>
            <td>{{ $resource->type->description }}</td>
            <td><a href="{{ $resource->link }}">{{ $resource->link }}</a></td>
            <td>{{ $resource->label }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

@endsection