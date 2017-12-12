@extends('layouts.app')

@section('content')
    <div class="jumbotron">
        <h1>AnimeThemes</h1>
        <p class="lead">A simple and consistent repository of anime opening and ending themes</p>
    </div>
    @include('layouts.announcements')
    @include('layouts.webm')
    @include('layouts.nav')
    @include('layouts.grill')

    <script type="text/javascript" src="{{ asset('js/vendor/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/modernizr.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/pomf.js') }}"></script>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection