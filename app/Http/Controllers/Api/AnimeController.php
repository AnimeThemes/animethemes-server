<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnimeCollection;
use App\Http\Resources\AnimeResource;
use App\Models\Anime;

class AnimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/anime/",
     *     operationId="getAnimes",
     *     tags={"Anime"},
     *     summary="Get paginated listing of Anime",
     *     description="Returns listing of Anime",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Items(ref="#/components/schemas/AnimeResource"))
     *     )
     * )
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new AnimeCollection(Anime::with('synonyms', 'series', 'themes', 'themes.entries', 'themes.entries.videos', 'themes.song', 'themes.song.artists', 'externalResources')->paginate());
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/anime/{alias}",
     *     operationId="getAnime",
     *     tags={"Anime"},
     *     summary="Get properties of Anime",
     *     description="Returns properties of Anime",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(ref="#/components/schemas/AnimeResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found!"
     *     )
     * )
     *
     * @param  \App\Models\Anime  $anime
     * @return \Illuminate\Http\Response
     */
    public function show(Anime $anime)
    {
        return new AnimeResource($anime->load('synonyms', 'series', 'themes', 'themes.entries', 'themes.entries.videos', 'themes.song', 'themes.song.artists', 'externalResources'));
    }
}
