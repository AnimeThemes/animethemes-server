<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnimeResource;
use App\Models\Anime;

class AnimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Anime  $anime
     * @return \Illuminate\Http\Response
     */
    public function show(Anime $anime)
    {
        return new AnimeResource($anime->load('synonyms', 'series', 'themes', 'themes.entries', 'themes.entries.videos', 'themes.song', 'themes.song.artists', 'externalResources'));
    }
}
