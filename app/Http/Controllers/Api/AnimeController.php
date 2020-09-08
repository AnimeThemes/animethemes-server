<?php

namespace App\Http\Controllers\Api;

use App\Enums\Season;
use App\Http\Resources\AnimeCollection;
use App\Http\Resources\AnimeResource;
use App\Models\Anime;

class AnimeController extends BaseController
{
    // constants for query parameters
    protected const YEAR_QUERY = 'year';
    protected const SEASON_QUERY = 'season';

    /**
     * The array of eager relations.
     *
     * @var array
     */
    protected const EAGER_RELATIONS = [
        'synonyms',
        'series',
        'themes',
        'themes.entries',
        'themes.entries.videos',
        'themes.song',
        'themes.song.artists',
        'externalResources'
    ];

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/anime/",
     *     operationId="getAnimes",
     *     tags={"Anime"},
     *     summary="Get paginated listing of Anime",
     *     description="Returns listing of Anime",
     *     @OA\Parameter(
     *         description="The search query. Mapping is to anime.name and anime.synonyms.text.",
     *         example="bakemonogatari",
     *         name="q",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Filter anime by year",
     *         example="2009",
     *         name="year",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="Filter anime by season. Case-insensitive options are Winter, Spring, Summer & Fall.",
     *         example="Summer",
     *         name="season",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Order anime by field. Case-insensitive options are anime_id, created_at, updated_at, alias, name, year & season.",
     *         example="updated_at",
     *         name="order",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Direction of anime ordering. Case-insensitive options are asc & desc.",
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
     *         example="anime.\*.name,\*.link",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Property(property="anime",type="array", @OA\Items(ref="#/components/schemas/AnimeResource")))
     *     )
     * )
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // query parameters
        $search_query = strval(request(static::SEARCH_QUERY));
        $year_query = strval(request(static::YEAR_QUERY));
        $season_query = strtoupper(request(static::SEASON_QUERY));

        // initialize builder
        $anime = empty($search_query) ? Anime::query() : Anime::search($search_query);

        // eager load relations
        $anime = $anime->with(static::EAGER_RELATIONS);

        // apply filters
        if (!empty($year_query)) {
            $anime = $anime->where(static::YEAR_QUERY, $year_query);
        }
        if (!empty($season_query) && Season::hasKey($season_query)) {
            $anime = $anime->where(static::SEASON_QUERY, Season::getValue($season_query));
        }

        // order by
        $anime = $this->applyOrdering($anime);

        // paginate
        $anime = $anime->paginate($this->getPerPageLimit());

        return new AnimeCollection($anime);
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
     *     @OA\Parameter(
     *         description="The comma-separated list of fields to include by dot notation. Wildcards are supported. If unset, all fields are included.",
     *         example="name,\*.link",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
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
        return new AnimeResource($anime->load(static::EAGER_RELATIONS));
    }
}
