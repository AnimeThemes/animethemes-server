<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $series = Series::all();
        return view('series.index')->withSeries($series);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $anime = Anime::all();
        return view('series.create')->withAnime($anime);
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
            'alias' => ['required', 'unique:series', 'max:192', 'alpha_dash'],
            'name' => ['required', 'max:192'],
            'anime' => ['exists:anime,anime_id'],
        ]);

        Series::create($request->all())->anime()->sync($request->input('anime'));

        return redirect()->route('series.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Series  $series
     * @return \Illuminate\Http\Response
     */
    public function show(Series $series)
    {
        return view('series.show')->withSeries($series);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Series  $series
     * @return \Illuminate\Http\Response
     */
    public function edit(Series $series)
    {
        $anime = Anime::all();
        return view('series.edit')->withSeries($series)->withAnime($anime);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Series  $series
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Series $series)
    {
        $request->validate([
            'alias' => ['required', Rule::unique('series')->ignore($series), 'max:192', 'alpha_dash'],
            'name' => ['required', 'max:192'],
            'anime' => ['exists:anime,anime_id'],
        ]);

        $series->anime()->sync($request->input('anime'));
        $series->update($request->all());

        return redirect()->route('series.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Series  $series
     * @return \Illuminate\Http\Response
     */
    public function destroy(Series $series)
    {
        $series->delete();

        return redirect()->route('series.index');
    }
}
