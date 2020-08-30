<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SongCollection;
use App\Http\Resources\SongResource;
use App\Models\Song;

class SongController extends BaseController
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
     *     @OA\Parameter(
     *         description="The number of resources to return per page. Acceptable range is [1-100]. Default value is 100.",
     *         example=50,
     *         name="limit",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields to include by dot notation. Wildcards are supported. If unset, all fields are included.",
     *         example="songs.\*.title,\*.name",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
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
        return new SongCollection(Song::with('themes', 'themes.anime', 'artists')->paginate($this->getPerPageLimit()));

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
     *     @OA\Parameter(
     *         description="The comma-separated list of fields to include by dot notation. Wildcards are supported. If unset, all fields are included.",
     *         example="title,\*.name",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
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

    /**
     * Search resources
     *
     * @OA\Get(
     *     path="/song/search",
     *     operationId="searchSongs",
     *     tags={"Song"},
     *     summary="Get paginated listing of Songs by search criteria",
     *     description="Returns listing of Songs by search criteria",
     *     @OA\Parameter(
     *         description="The search query. Mapping is to song.title.",
     *         example="stable staple",
     *         name="q",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The number of resources to return per page. Acceptable range is [1-100]. Default value is 100.",
     *         example=50,
     *         name="limit",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields to include by dot notation. Wildcards are supported. If unset, all fields are included.",
     *         example="songs.\*.title,\*.name",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Property(property="songs",type="array", @OA\Items(ref="#/components/schemas/SongResource")))
     *     )
     * )
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        $songs = [];
        $search_query = strval(request('q'));
        if (!empty($search_query)) {
            $songs = Song::search($search_query)->query(function ($builder) {
                $builder->with('themes', 'themes.anime', 'artists');
            })->paginate($this->getPerPageLimit());
        }
        return new SongCollection($songs);

    }
}
