<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Wiki\Anime\AnimeIndexRequest;
use App\Http\Requests\Api\Wiki\Anime\AnimeShowRequest;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use Illuminate\Http\JsonResponse;

/**
 * Class AnimeController.
 */
class AnimeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param  AnimeIndexRequest  $request
     * @return JsonResponse
     */
    public function index(AnimeIndexRequest $request): JsonResponse
    {
        if ($this->query->hasSearchCriteria()) {
            return AnimeCollection::performSearch($this->query, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return AnimeCollection::performQuery($this->query)->toResponse($request);
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
        $resource = AnimeResource::performQuery($anime, $this->query);

        return $resource->toResponse($request);
    }
}
