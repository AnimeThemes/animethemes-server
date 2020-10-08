<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SeriesCollection;
use App\Http\Resources\SeriesResource;
use App\Models\Series;

class SeriesController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/series/",
     *     operationId="getSerie",
     *     tags={"Series"},
     *     summary="Get paginated listing of Series",
     *     description="Returns listing of Series",
     *     @OA\Parameter(
     *         description="The search query. Mapping is to series.name.",
     *         example="Monogatari",
     *         name="q",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is anime.synonyms, anime.themes, anime.themes.entries, anime.themes.entries.videos, anime.themes.song, anime.themes.song.artists & anime.externalResources.",
     *         example="anime.synonyms,anime.themes",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Order series by field. Case-insensitive options are artist_id, created_at, updated_at, alias & name.",
     *         example="updated_at",
     *         name="order",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Direction of series ordering. Case-insensitive options are asc & desc.",
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
     *         example="series.\*.name,\*.alias",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Property(property="series",type="array", @OA\Items(ref="#/components/schemas/SeriesResource")))
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // query parameters
        $search_query = strval(request(static::SEARCH_QUERY));

        // initialize builder
        $series = empty($search_query) ? Series::query() : Series::search($search_query);

        // eager load relations
        $series = $series->with($this->getIncludePaths());

        // order by
        $series = $this->applyOrdering($series);

        // paginate
        $series = $series->paginate($this->getPerPageLimit());

        $collection = new SeriesCollection($series);
        return $collection->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/series/{alias}",
     *     operationId="getSeries",
     *     tags={"Series"},
     *     summary="Get properties of Series",
     *     description="Returns properties of Series",
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is anime.synonyms, anime.themes, anime.themes.entries, anime.themes.entries.videos, anime.themes.song, anime.themes.song.artists & anime.externalResources.",
     *         example="anime.synonyms,anime.themes",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
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
     *         @OA\JsonContent(ref="#/components/schemas/SeriesResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found!"
     *     )
     * )
     *
     * @param  \App\Models\Series  $series
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Series $series)
    {
        $resource = new SeriesResource($series->load($this->getIncludePaths()));
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
            'anime',
            'anime.synonyms',
            'anime.themes',
            'anime.themes.entries',
            'anime.themes.entries.videos',
            'anime.themes.song',
            'anime.themes.song.artists',
            'anime.externalResources'
        ];
    }
}
