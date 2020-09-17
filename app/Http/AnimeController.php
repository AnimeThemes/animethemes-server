<?php

namespace App\Http\Controllers;

use App\Models\Anime;

class AnimeController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Anime  $anime
     * @return \Illuminate\View\View
     */
    public function show(Anime $anime)
    {
        return view('anime.show')->with('anime', $anime);
    }
}
