<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Anime\YearIndexRequest;
use App\Http\Requests\Api\Wiki\Anime\YearShowRequest;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\RouteDiscovery\Attributes\Route;

/**
 * Class YearController.
 */
class YearController extends Controller
{
    /**
     * Display a listing of unique years of anime.
     *
     * @param  YearIndexRequest  $request
     * @return JsonResponse
     *
     * @noinspection PhpUnusedParameterInspection
     */
    #[Route(fullUri: 'animeyear', name: 'animeyear.index')]
    public function index(YearIndexRequest $request): JsonResponse
    {
        return new JsonResponse(
            Anime::query()
                ->distinct(Anime::ATTRIBUTE_YEAR)
                ->orderBy(Anime::ATTRIBUTE_YEAR)
                ->pluck(Anime::ATTRIBUTE_YEAR)
        );
    }

    /**
     * Display a listing of anime of year by season.
     *
     * @param  YearShowRequest  $request
     * @param  string  $year
     * @return JsonResponse
     */
    #[Route(fullUri: 'animeyear/{year}', name: 'animeyear.show')]
    public function show(YearShowRequest $request, string $year): JsonResponse
    {
        $includeCriteria = $request->getQuery()->getIncludeCriteria(AnimeCollection::$wrap);

        $allowedIncludePaths = collect($includeCriteria?->getPaths());

        $anime = AnimeCollection::make(
            Anime::query()
                ->where(Anime::ATTRIBUTE_YEAR, $year)
                ->with($allowedIncludePaths->all())
                ->orderBy(Anime::ATTRIBUTE_NAME)
                ->get(),
            $request->getQuery()
        );

        $anime = collect($anime->toArray($request));

        $anime = $anime->groupBy(fn (AnimeResource $anime) => Str::lower(AnimeSeason::getDescription($anime->season)));

        $anime = $anime->sortBy(
            fn (Collection $seasonAnime, string $seasonKey) => AnimeSeason::getValue(Str::upper($seasonKey))
        );

        return new JsonResponse($anime);
    }
}
