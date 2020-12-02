<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AnimeCollection;
use App\Http\Resources\AnimeResource;
use App\Models\Anime;
use Illuminate\Support\Str;

class AnimeController extends BaseController
{
    // constants for query parameters
    public const YEAR_QUERY = 'year';
    public const SEASON_QUERY = 'season';

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
     *         description="Comma-separated list of included related resources. Allowed list is synonyms, series, themes, themes.entries, themes.entries.videos, themes.song, themes.song.artists, externalResources & images.",
     *         example="include=synonyms,series",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Filter anime by year",
     *         example="filter[year]=2009",
     *         name="year",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="Filter anime by season. Case-insensitive options are Winter, Spring, Summer & Fall.",
     *         example="filter[season]=Summer",
     *         name="season",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Sort anime resource collection by fields. Case-insensitive options are anime_id, created_at, updated_at, slug, name, year & season.",
     *         example="sort=-year,name",
     *         name="sort",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The number of resources to return per page. Acceptable range is [1-30]. Default value is 30.",
     *         example="page[size]=25",
     *         name="page[size]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="The page of resources to return.",
     *         example="page[number]=2",
     *         name="page[number]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[anime]=name,slug",
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
        // initialize builder with eager loaded relations
        $anime = Anime::with($this->parser->getIncludePaths(Anime::$allowedIncludePaths));

        // apply filters
        $anime = Anime::applyFilters($anime, $this->parser);

        // apply sorts
        foreach ($this->parser->getSorts() as $field => $isAsc) {
            if (in_array(Str::lower($field), Anime::$allowedSortFields)) {
                $anime = $anime->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
            }
        }

        // paginate
        $anime = $anime->jsonPaginate();

        $collection = AnimeCollection::make($anime, $this->parser);

        return $collection->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/anime/{slug}",
     *     operationId="getAnime",
     *     tags={"Anime"},
     *     summary="Get properties of Anime",
     *     description="Returns properties of Anime",
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is synonyms, series, themes, themes.entries, themes.entries.videos, themes.song, themes.song.artists, externalResources & images.",
     *         example="include=synonyms,series",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[anime]=name,slug",
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
        $resource = AnimeResource::make($anime->load($this->parser->getIncludePaths(Anime::$allowedIncludePaths)), $this->parser);

        return $resource->toResponse(request());
    }
}
