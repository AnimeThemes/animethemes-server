<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EntryResource;
use App\Models\Entry;

class EntryController extends Controller
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
     * @OA\Get(
     *     path="/entry/{id}",
     *     operationId="getEntry",
     *     tags={"Entry"},
     *     summary="Get properties of Entry",
     *     description="Returns properties of Entry",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(ref="#/components/schemas/EntryResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Entry cannot be found"
     *     )
     * )
     *
     * @param  \App\Models\Entry  $entry
     * @return \Illuminate\Http\Response
     */
    public function show(Entry $entry)
    {
        return new EntryResource($entry->load('anime', 'theme', 'videos'));
    }
}
