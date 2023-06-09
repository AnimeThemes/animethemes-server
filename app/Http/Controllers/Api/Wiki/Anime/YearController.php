<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Anime;

use App\Contracts\Http\Api\InteractsWithSchema;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Anime\YearShowRequest;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * Class YearController.
 */
class YearController extends Controller implements InteractsWithSchema
{
    /**
     * Display a listing of unique years of anime.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
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
    public function show(YearShowRequest $request, string $year): JsonResponse
    {
        $query = new Query($request->validated());

        $includeCriteria = $query->getIncludeCriteria(AnimeCollection::$wrap);

        $allowedIncludePaths = collect($includeCriteria?->getPaths());

        $anime = new AnimeCollection(
            Anime::query()
                ->where(Anime::ATTRIBUTE_YEAR, $year)
                ->with($allowedIncludePaths->all())
                ->orderBy(Anime::ATTRIBUTE_NAME)
                ->get(),
            $query
        );

        $anime = collect($anime->toArray($request));

        $anime = $anime->groupBy(fn (AnimeResource $anime) => Str::lower($anime->season->localize()));

        return new JsonResponse($anime);
    }

    /**
     * Get the underlying schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new AnimeSchema();
    }
}
