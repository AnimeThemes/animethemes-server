<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        @include('layouts.analytics')
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'AnimeThemes') }}</title>
        <link rel="stylesheet" href="{{ asset('css/animethemes.css') }}">
    </head>
    <body>
        @include('layouts.navigation')
        <div class="container">
            @yield('content')
        </div>
        <script src="{{ asset('js/animethemes.js') }}"></script>
    </body>
</html>
