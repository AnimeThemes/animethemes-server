<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
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
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        if ($this->parser->hasSearch()) {
            return AnimeCollection::performSearch($this->parser, PaginationStrategy::OFFSET())->toResponse(request());
        }

        return AnimeCollection::performQuery($this->parser)->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @param Anime $anime
     * @return JsonResponse
     */
    public function show(Anime $anime): JsonResponse
    {
        $resource = AnimeResource::performQuery($anime, $this->parser);

        return $resource->toResponse(request());
    }
}
