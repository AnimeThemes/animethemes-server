<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\Synonym;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SynonymController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($anime_alias)
    {
        $synonyms = Synonym::where('anime_id', function ($query) use ($anime_alias) {
            $query->select('anime_id')->from('anime')->where('alias', $anime_alias);
        })->get();
        return view('synonym.index', compact('synonyms', 'anime_alias'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($anime_alias)
    {
        return view('synonym.create', compact('anime_alias'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($anime_alias, Request $request)
    {
        $anime = Anime::where('alias', $anime_alias)->firstOrFail();

        $request->validate([
            'text' => ['required', 'max:192', Rule::unique('synonym', 'text')->where(function ($query) use ($anime) {
                return $query->where('anime_id', $anime->anime_id);
            })],
        ]);

        $anime->synonyms()->create($request->all());

        return redirect()->route('anime.synonym.index', $anime_alias);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Synonym  $synonym
     * @return \Illuminate\Http\Response
     */
    public function show($anime_alias, Synonym $synonym)
    {
        return view('synonym.show')->withSynonym($synonym);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Synonym  $synonym
     * @return \Illuminate\Http\Response
     */
    public function edit($anime_alias, Synonym $synonym)
    {
        return view('synonym.edit', compact('anime_alias', 'synonym'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Synonym  $synonym
     * @return \Illuminate\Http\Response
     */
    public function update($anime_alias, Request $request, Synonym $synonym)
    {
        $anime = Anime::where('alias', $anime_alias)->firstOrFail();

        $request->validate([
            'text' => ['required', 'max:192', Rule::unique('synonym', 'text')->where(function ($query) use ($anime) {
                return $query->where('anime_id', $anime->anime_id);
            })->ignore($synonym)],
        ]);

        $synonym->update($request->all());

        return redirect()->route('anime.synonym.index', $anime_alias);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Synonym  $synonym
     * @return \Illuminate\Http\Response
     */
    public function destroy($anime_alias, Synonym $synonym)
    {
        $synonym->delete();

        return redirect()->route('anime.synonym.index', $anime_alias);
    }
}
