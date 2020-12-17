<?php

namespace App\Http\Controllers\Api;

use App\Enums\AnimeSeason;
use App\Http\Resources\AnimeCollection;
use App\Models\Anime;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class YearController extends BaseController
{
    // constants for query parameters
    protected const NAME_QUERY = 'name';
    protected const YEAR_QUERY = 'year';

    /**
     * Display a listing of unique years of anime.
     *
     * @OA\Get(
     *     path="/year/",
     *     operationId="getYears",
     *     tags={"Anime"},
     *     summary="Get list of unique years of anime",
     *     description="Returns list of unique years",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Items(type="integer",example=2009)))
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return new JsonResponse(Anime::distinct(static::YEAR_QUERY)->orderBy(static::YEAR_QUERY)->pluck(static::YEAR_QUERY));
    }

    /**
     * Display a listing of anime of year by season.
     *
     * @OA\Get(
     *     path="/year/{year}",
     *     operationId="getAnimeOfYearBySeason",
     *     tags={"Anime"},
     *     summary="Get listing of Anime of year by season",
     *     description="Returns listing of Anime of year by season",
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is synonyms, series, themes, themes.entries, themes.entries.videos, themes.song, themes.song.artists & externalResources.",
     *         example="include=synonyms,series",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Property(property="summer",type="array", @OA\Items(ref="#/components/schemas/AnimeResource")))
     *     )
     * )
     *
     * @param string $year
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($year)
    {
        $anime = AnimeCollection::make(
            Anime::where(static::YEAR_QUERY, $year)
                ->with($this->parser->getIncludePaths(Anime::$allowedIncludePaths))
                ->orderBy(static::NAME_QUERY)
                ->get(),
            $this->parser
        );

        $anime = collect($anime->toArray(request()));

        $anime = $anime->groupBy(function ($item) {
            return Str::lower(AnimeSeason::getDescription($item->season));
        });

        $anime = $anime->sortBy(function ($season_anime, $season_key) {
            return AnimeSeason::getValue(Str::upper($season_key));
        });

        return new JsonResponse($anime);
    }
}