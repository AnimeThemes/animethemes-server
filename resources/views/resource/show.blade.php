@extends('layouts.app')

@section('content')

<div>
    <strong>Type:</strong>
    {{ $resource->type->description }}
</div>
<div>
    <strong>Link:</strong>
    {{ $resource->link }}
</div>
<div>
    <strong>Label:</strong>
    {{ $resource->label }}
</div>

@endsection