<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        return new JsonResponse(Anime::query()->distinct('year')->orderBy('year')->pluck('year'));
    }

    /**
     * Display a listing of anime of year by season.
     *
     * @param Request $request
     * @param string $year
     * @return JsonResponse
     */
    public function show(Request $request, string $year): JsonResponse
    {
        $includeCriteria = $this->query->getIncludeCriteria(AnimeCollection::$wrap);

        $allowedIncludePaths = collect($includeCriteria?->getAllowedPaths(AnimeCollection::allowedIncludePaths()));

        $anime = AnimeCollection::make(
            Anime::query()
                ->where('year', $year)
                ->with($allowedIncludePaths->all())
                ->orderBy('name')
                ->get(),
            $this->query
        );

        $anime = collect($anime->toArray($request));

        $anime = $anime->groupBy(function (AnimeResource $anime) {
            return Str::lower(AnimeSeason::getDescription($anime->season));
        });

        $anime = $anime->sortBy(function (Collection $seasonAnime, string $seasonKey) {
            return AnimeSeason::getValue(Str::upper($seasonKey));
        });

        return new JsonResponse($anime);
    }
}
