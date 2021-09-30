<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Anime\AnimeIndexRequest;
use App\Http\Requests\Api\Wiki\Anime\AnimeShowRequest;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use Illuminate\Http\JsonResponse;

/**
 * Class AnimeController.
 */
class AnimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  AnimeIndexRequest  $request
     * @return JsonResponse
     */
    public function index(AnimeIndexRequest $request): JsonResponse
    {
        $query = $request->getQuery();

        if ($query->hasSearchCriteria()) {
            return AnimeCollection::performSearch($query, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return AnimeCollection::performQuery($query)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  AnimeShowRequest  $request
     * @param  Anime  $anime
     * @return JsonResponse
     */
    public function show(AnimeShowRequest $request, Anime $anime): JsonResponse
    {
        $resource = AnimeResource::performQuery($anime, $request->getQuery());

        return $resource->toResponse($request);
    }
}
