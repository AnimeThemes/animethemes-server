@extends('layouts.app')

@section('content')

@foreach ($series as $theSeries)
    <p><a href="{{ route('series.show',$theSeries->alias) }}">{{ $theSeries->name }}</a></p>
@endforeach

@endsection