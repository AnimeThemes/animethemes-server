<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Song;
use Illuminate\Http\Request;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $songs = Song::all();
        return view('song.index')->withSongs($songs);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $artists = Artist::all();
        return view('song.create')->withArtists($artists);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['nullable', 'max:192'],
            'by' => ['nullable', 'max:192'],
            'artists' => ['exists:artist,artist_id'],
        ]);

        Song::create($request->all())->artists()->sync($request->input('artists'));

        return redirect()->route('song.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Song  $song
     * @return \Illuminate\Http\Response
     */
    public function show(Song $song)
    {
        return view('song.show')->withSong($song);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Song  $song
     * @return \Illuminate\Http\Response
     */
    public function edit(Song $song)
    {
        $artists = Artist::all();
        return view('song.edit')->withSong($song)->withArtists($artists);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Song  $song
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Song $song)
    {
        $request->validate([
            'title' => ['nullable', 'max:192'],
            'by' => ['nullable', 'max:192'],
            'artists' => ['exists:artist,artist_id'],
        ]);

        $song->artists()->sync($request->input('artists'));
        $song->update($request->all());

        return redirect()->route('song.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Song  $song
     * @return \Illuminate\Http\Response
     */
    public function destroy(Song $song)
    {
        $song->delete();

        return redirect()->route('song.index');
    }
}
