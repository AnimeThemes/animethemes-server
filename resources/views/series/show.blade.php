@extends('layouts.app')

@section('content')

<div>
    <strong>Alias:</strong>
    {{$series->alias}}
</div>
<div>
    <strong>Name:</strong>
    {{$series->name}}
</div>

@endsection