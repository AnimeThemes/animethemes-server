@extends('layouts.app')

@section('content')

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>By</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($songs as $song)
        <tr>
            <td><a href="{{ route('song.show', $song->song_id) }}">{{ $song->song_id }}</a></td>
            <td>{{ $song->title }}</td>
            <td>{{ $song->by }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

@endsection