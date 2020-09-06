<?php

namespace App\Http\Controllers\Api;

use App\Enums\Season;
use App\Http\Resources\AnimeCollection;
use App\Models\Anime;

class YearController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/anime/{year}/all",
     *     operationId="getAnimeByYear",
     *     tags={"Anime"},
     *     summary="Get paginated listing of Anime of year",
     *     description="Returns listing of Anime of year",
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
    public function all($year) {
        return new AnimeCollection(Anime::where('year', $year)->with('synonyms', 'series', 'themes', 'themes.entries', 'themes.entries.videos', 'themes.song', 'themes.song.artists', 'externalResources')->paginate($this->getPerPageLimit()));
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
            ->get()))->groupBy(function ($item) {
            return Season::getDescription($item->season);
        });
    }

    /**
     * Search resources
     *
     * @OA\Get(
     *     path="/anime/{year}/search",
     *     operationId="searchAnimeByYear",
     *     tags={"Anime"},
     *     summary="Get paginated listing of Anime of year by search criteria",
     *     description="Returns listing of Anime of year by search criteria",
     *     @OA\Parameter(
     *         description="The search query. Mapping is to anime.name and anime.synonyms.text.",
     *         example="bakemonogatari",
     *         name="q",
     *         in="query",
     *         required=true,
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
    public function search()
    {
        $anime = [];
        $search_query = strval(request('q'));
        if (!empty($search_query)) {
            $anime = Anime::search($search_query)
                ->where('year', request('year'))
                ->with(['synonyms', 'series', 'themes', 'themes.entries', 'themes.entries.videos', 'themes.song', 'themes.song.artists', 'externalResources'])
                ->paginate($this->getPerPageLimit());
        }
        return new AnimeCollection($anime);
    }
}
