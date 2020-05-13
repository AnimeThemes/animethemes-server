<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\ExternalResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $artists = Artist::all();
        return view('artist.index')->withArtists($artists);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $resources = ExternalResource::all();
        return view('artist.create')->withResources($resources);
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
            'alias' => ['required', 'unique:artist', 'max:192', 'alpha_dash'],
            'name' => ['required', 'max:192'],
            'resources' => ['exists:resource,resource_id'],
        ]);

        Artist::create($request->all())->externalResources()->sync($request->input('resources'));

        return redirect()->route('artist.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function show(Artist $artist)
    {
        return view('artist.show')->withArtist($artist);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function edit(Artist $artist)
    {
        $resources = ExternalResource::all();
        return view('artist.edit')->withArtist($artist)->withResources($resources);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Artist $artist)
    {
        $request->validate([
            'alias' => ['required', Rule::unique('artist')->ignore($artist), 'max:192', 'alpha_dash'],
            'name' => ['required', 'max:192'],
            'resources' => ['exists:resource,resource_id'],
        ]);

        $artist->externalResources()->sync($request->input('resources'));
        $artist->update($request->all());

        return redirect()->route('artist.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Artist $artist)
    {
        $artist->delete();

        return redirect()->route('artist.index');
    }
}
