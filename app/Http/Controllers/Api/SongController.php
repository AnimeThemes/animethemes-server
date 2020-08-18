<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SongResource;
use App\Models\Song;

class SongController extends Controller
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
     *     path="/song/{id}",
     *     operationId="getSong",
     *     tags={"Song"},
     *     summary="Get properties of Song",
     *     description="Returns properties of Song",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(ref="#/components/schemas/SongResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Song cannot be found"
     *     )
     * )
     *
     * @param  \App\Models\Song  $song
     * @return \Illuminate\Http\Response
     */
    public function show(Song $song)
    {
        return new SongResource($song->load('themes', 'themes.anime', 'artists'));
    }
}
