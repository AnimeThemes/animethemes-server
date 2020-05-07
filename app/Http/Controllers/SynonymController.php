<?php

namespace App\Http\Controllers;

use App\Models\Synonym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Synonym  $synonym
     * @return \Illuminate\Http\Response
     */
    public function show($anime_alias, Synonym $synonym)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Synonym  $synonym
     * @return \Illuminate\Http\Response
     */
    public function edit($anime_alias, Synonym $synonym)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Synonym  $synonym
     * @return \Illuminate\Http\Response
     */
    public function destroy($anime_alias, Synonym $synonym)
    {
        //
    }
}
