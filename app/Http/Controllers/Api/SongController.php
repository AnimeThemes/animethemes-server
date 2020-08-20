<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SongCollection;
use App\Http\Resources\SongResource;
use App\Models\Song;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/song/",
     *     operationId="getSongs",
     *     tags={"Song"},
     *     summary="Get paginated listing of Songs",
     *     description="Returns listing of Songs",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Property(property="songs",type="array", @OA\Items(ref="#/components/schemas/SongResource")))
     *     )
     * )
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new SongCollection(Song::with('themes', 'themes.anime', 'artists')->paginate());

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
     *         description="Resource Not Found!"
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
