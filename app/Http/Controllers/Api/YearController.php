<?php

namespace App\Http\Controllers\Api;

use App\Enums\Season;
use App\Http\Resources\AnimeCollection;
use App\Models\Anime;

class YearController extends AnimeController
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
    public function year($year) {
        return (new AnimeCollection(Anime::where(static::YEAR_QUERY, $year)
            ->with(static::EAGER_RELATIONS)
            ->orderBy(static::NAME_QUERY)
            ->get()
        ))->groupBy(function ($item) {
            return Season::getDescription($item->season);
        });
    }
}
