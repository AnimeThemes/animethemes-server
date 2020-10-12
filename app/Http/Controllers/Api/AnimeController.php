<?php

namespace App\Http\Controllers\Api;

use App\Enums\Season;
use App\Http\Resources\AnimeCollection;
use App\Http\Resources\AnimeResource;
use App\Models\Anime;
use Illuminate\Support\Str;

class AnimeController extends BaseController
{
    // constants for query parameters
    protected const NAME_QUERY = 'name';
    protected const YEAR_QUERY = 'year';
    protected const SEASON_QUERY = 'season';

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
     *         description="Comma-separated list of included related resources. Allowed list is synonyms, series, themes, themes.entries, themes.entries.videos, themes.song, themes.song.artists & externalResources.",
     *         example="synonyms,series",
     *         name="include",
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
     *         description="Sort anime resource collection by fields. Case-insensitive options are anime_id, created_at, updated_at, alias, name, year & season.",
     *         example="-year,name",
     *         name="sort",
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
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[anime]=name,alias",
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // query parameters
        $search_query = strval(request(static::SEARCH_QUERY));
        $year_query = strval(request(static::YEAR_QUERY));
        $season_query = Str::upper(request(static::SEASON_QUERY));

        // initialize builder
        $anime = empty($search_query) ? Anime::query() : Anime::search($search_query);

        // eager load relations
        $anime = $anime->with($this->getIncludePaths());

        // apply filters
        if (! empty($year_query)) {
            $anime = $anime->where(static::YEAR_QUERY, $year_query);
        }
        if (! empty($season_query) && Season::hasKey($season_query)) {
            $anime = $anime->where(static::SEASON_QUERY, Season::getValue($season_query));
        }

        // apply sorts
        $anime = $this->applySorting($anime);

        // paginate
        $anime = $anime->paginate($this->getPerPageLimit());

        $collection = new AnimeCollection($anime, $this->getFieldSets());

        return $collection->toResponse(request());
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
     *         description="Comma-separated list of included related resources. Allowed list is synonyms, series, themes, themes.entries, themes.entries.videos, themes.song, themes.song.artists & externalResources.",
     *         example="synonyms,series",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[anime]=name,alias",
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Anime $anime)
    {
        $resource = new AnimeResource($anime->load($this->getIncludePaths()), $this->getFieldSets());

        return $resource->toResponse(request());
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return array
     */
    public static function getAllowedIncludePaths()
    {
        return [
            'synonyms',
            'series',
            'themes',
            'themes.entries',
            'themes.entries.videos',
            'themes.song',
            'themes.song.artists',
            'externalResources',
        ];
    }

    /**
     * The sort field names a client is allowed to request.
     *
     * @return array
     */
    public static function getAllowedSortFields()
    {
        return [
            'anime_id',
            'created_at',
            'updated_at',
            'alias',
            'name',
            'year',
            'season',
        ];
    }
}
