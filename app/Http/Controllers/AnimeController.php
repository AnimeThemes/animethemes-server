<?php

namespace App\Http\Controllers;

use App\Enums\Season;
use App\Models\Anime;
use App\Models\ExternalResource;
use App\Rules\YearRange;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $anime = Anime::all();
        return view('anime.index')->withAnime($anime);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $resources = ExternalResource::all();
        return view('anime.create')->withSeasons(Season::toSelectArray())->withResources($resources);
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
            'alias' => ['required', 'unique:anime', 'max:192', 'alpha_dash'],
            'name' => ['required', 'max:192'],
            'year' => ['required', 'digits:4', 'integer', new YearRange],
            'season' => ['required', new EnumValue(Season::class, false)],
            'resources' => ['exists:resource,resource_id'],
        ]);

        Anime::create($request->all())->externalResources()->sync($request->input('resources'));

        return redirect()->route('anime.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Anime  $anime
     * @return \Illuminate\Http\Response
     */
    public function show(Anime $anime)
    {
        return view('anime.show')->withAnime($anime);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Anime  $anime
     * @return \Illuminate\Http\Response
     */
    public function edit(Anime $anime)
    {
        $resources = ExternalResource::all();
        return view('anime.edit')->withAnime($anime)->withSeasons(Season::toSelectArray())->withResources($resources);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Anime  $anime
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Anime $anime)
    {
        $request->validate([
            'alias' => ['required', Rule::unique('anime')->ignore($anime), 'max:192', 'alpha_dash'],
            'name' => ['required', 'max:192'],
            'year' => ['required', 'digits:4', 'integer', new YearRange],
            'season' => ['required', new EnumValue(Season::class, false)],
            'resources' => ['exists:resource,resource_id'],
        ]);

        $anime->externalResources()->sync($request->input('resources'));
        $anime->update($request->all());

        return redirect()->route('anime.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Anime  $anime
     * @return \Illuminate\Http\Response
     */
    public function destroy(Anime $anime)
    {
        $anime->delete();

        return redirect()->route('anime.index');
    }
}
