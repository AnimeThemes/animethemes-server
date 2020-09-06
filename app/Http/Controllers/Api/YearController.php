<?php

namespace App\Http\Controllers\Api;

use App\Enums\Season;
use App\Http\Resources\AnimeCollection;
use App\Models\Anime;

class YearController extends BaseController
{
    /**
     * Display a listing of anime of year by season.
     *
     * @OA\Get(
     *     path="/year/{year}",
     *     operationId="getAnimeOfYearBySeason",
     *     tags={"Anime"},
     *     summary="Get listing of Anime of year by season",
     *     description="Returns listing of Anime of year by season",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Property(property="summer",type="array", @OA\Items(ref="#/components/schemas/AnimeResource")))
     *     )
     * )
     *
     * @return \Illuminate\Http\Response
     */
    public function show($year) {
        return (new AnimeCollection(Anime::where('year', $year)
            ->with('synonyms', 'series', 'themes', 'themes.entries', 'themes.entries.videos', 'themes.song', 'themes.song.artists', 'externalResources')
            ->orderBy('name')
            ->get()
        ))->groupBy(function ($item) {
            return Season::getDescription($item->season);
        });
    }
}
