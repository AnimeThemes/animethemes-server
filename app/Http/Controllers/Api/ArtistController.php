<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ArtistCollection;
use App\Http\Resources\ArtistResource;
use App\Models\Artist;

class ArtistController extends BaseController
{
    /**
     * The array of eager relations.
     *
     * @var array
     */
    protected const EAGER_RELATIONS = [
        'songs',
        'songs.themes',
        'songs.themes.anime',
        'members',
        'groups',
        'externalResources'
    ];

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/artist/",
     *     operationId="getArtists",
     *     tags={"Artist"},
     *     summary="Get paginated listing of Artists",
     *     description="Returns listing of Artists",
     *     @OA\Parameter(
     *         description="The search query. Mapping is to artist.name and artist.songs.pivot.as.",
     *         example="Senjougahara",
     *         name="q",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Order artists by field. Case-insensitive options are artist_id, created_at, updated_at, alias & name.",
     *         example="updated_at",
     *         name="order",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Direction of artist ordering. Case-insensitive options are asc & desc.",
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
     *         example="artists.\*.name,\*.alias",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Property(property="artists",type="array", @OA\Items(ref="#/components/schemas/ArtistResource")))
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
        $artists = empty($search_query) ? Artist::query() : Artist::search($search_query);

        // eager load relations
        $artists = $artists->with(static::EAGER_RELATIONS);

        // order by
        $artists = $this->applyOrdering($artists);

        // paginate
        $artists = $artists->paginate($this->getPerPageLimit());

        return new ArtistCollection($artists);
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
     *     @OA\Parameter(
     *         description="The comma-separated list of fields to include by dot notation. Wildcards are supported. If unset, all fields are included.",
     *         example="name,\*.alias",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(ref="#/components/schemas/ArtistResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found!"
     *     )
     * )
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function show(Artist $artist)
    {
        return new ArtistResource($artist->load(static::EAGER_RELATIONS));
    }
}
