@extends('layouts.app')

@section('content')
    <div class="jumbotron">
        <h1>AnimeThemes</h1>
        <p class="lead">A simple and consistent repository of anime opening and ending themes</p>
    </div>
    @include('layouts.announcements')
    @include('layouts.webm')
    @include('layouts.nav')

    <br>

    @foreach ($videos as $video)
    <p><a href="{{ route('video.show', ['alias' => $video->basename]) }}">{{ $video->filename }}</a></p>
    @endforeach

    <nav>{{ $videos->links() }}</nav>
@endsection