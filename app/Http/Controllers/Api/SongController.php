<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SongCollection;
use App\Http\Resources\SongResource;
use App\Models\Song;

class SongController extends BaseController
{
    /**
     * The array of eager relations.
     *
     * @var array
     */
    protected const EAGER_RELATIONS = [
        'themes',
        'themes.anime',
        'artists'
    ];

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
     *         description="The search query. Mapping is to song.title.",
     *         example="stable staple",
     *         name="q",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Order songs by field. Case-insensitive options are song_id, created_at, updated_at & title.",
     *         example="updated_at",
     *         name="order",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Direction of song ordering. Case-insensitive options are asc & desc.",
     *         example="desc",
     *         name="direction",
     *         in="query",
     *         required=false,
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
    public function index()
    {
        // query parameters
        $search_query = strval(request(static::SEARCH_QUERY));

        // initialize builder
        $songs = empty($search_query) ? Song::query() : Song::search($search_query);

        // eager load relations
        $songs = $songs->with(static::EAGER_RELATIONS);

        // order by
        $songs = $this->applyOrdering($songs);

        // paginate
        $songs = $songs->paginate($this->getPerPageLimit());

        return new SongCollection($songs);
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
        return new SongResource($song->load(static::EAGER_RELATIONS));
    }
}
