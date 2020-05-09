@extends('layouts.app')

@section('content')

<div>
    <strong>Alias:</strong>
    {{$anime->alias}}
</div>
<div>
    <strong>Name:</strong>
    {{$anime->name}}
</div>
<div>
    <strong>Year:</strong>
    {{ $anime->year }}
</div>
<div>
    <strong>Season:</strong>
    {{ $anime->season->description }}
</div>

@endsection