<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeriesResource;
use App\Models\Series;

class SeriesController extends Controller
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
     * @param  \App\Models\Series  $series
     * @return \Illuminate\Http\Response
     */
    public function show(Series $series)
    {
        return new SeriesResource($series->load('anime', 'anime.synonyms', 'anime.themes', 'anime.themes.entries', 'anime.themes.entries.videos', 'anime.themes.song', 'anime.themes.song.artists', 'anime.externalResources'));
    }
}
