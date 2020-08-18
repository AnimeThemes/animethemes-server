<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArtistResource;
use App\Models\Artist;

class ArtistController extends Controller
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
     *     path="/artist/{alias}",
     *     operationId="getArtist",
     *     tags={"Artist"},
     *     summary="Get properties of Artist",
     *     description="Returns properties of Artist",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(ref="#/components/schemas/ArtistResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Artist cannot be found"
     *     )
     * )
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function show(Artist $artist)
    {
        return new ArtistResource($artist->load('songs', 'songs.themes', 'songs.themes.anime', 'members', 'groups', 'externalResources'));
    }
}
