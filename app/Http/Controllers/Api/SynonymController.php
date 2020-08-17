<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SynonymResource;
use App\Models\Synonym;

class SynonymController extends Controller
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
     * @param  \App\Models\Synonym  $synonym
     * @return \Illuminate\Http\Response
     */
    public function show(Synonym $synonym)
    {
        return new SynonymResource($synonym->load('anime'));
    }
}
