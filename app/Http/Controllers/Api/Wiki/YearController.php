<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class YearController.
 */
class YearController extends BaseController
{
    /**
     * Display a listing of unique years of anime.
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
