<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\AnimeSeason;
use App\Http\Resources\AnimeCollection;
use App\Http\Resources\AnimeResource;
use App\Models\Anime;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class YearController
 * @package App\Http\Controllers\Api
 */
class YearController extends BaseController
{
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
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return new JsonResponse(Anime::distinct('year')->orderBy('year')->pluck('year'));
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
     * @return JsonResponse
     */
    public function show(string $year): JsonResponse
    {
        $anime = AnimeCollection::make(
            Anime::where('year', $year)
                ->with($this->parser->getIncludePaths(AnimeCollection::allowedIncludePaths()))
                ->orderBy('name')
                ->get(),
            $this->parser
        );

        $anime = collect($anime->toArray(request()));

        $anime = $anime->groupBy(function (AnimeResource $anime) {
            return Str::lower(AnimeSeason::getDescription($anime->season));
        });

        $anime = $anime->sortBy(function (Collection $seasonAnime, string $seasonKey) {
            return AnimeSeason::getValue(Str::upper($seasonKey));
        });

        return new JsonResponse($anime);
    }
}
