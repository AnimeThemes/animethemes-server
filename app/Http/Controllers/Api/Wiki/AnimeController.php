<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class AnimeController.
 */
class AnimeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if ($this->parser->hasSearch()) {
            return AnimeCollection::performSearch($this->parser, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return AnimeCollection::performQuery($this->parser)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Anime $anime
     * @return JsonResponse
     */
    public function show(Request $request, Anime $anime): JsonResponse
    {
        $resource = AnimeResource::performQuery($anime, $this->parser);

        return $resource->toResponse($request);
    }
}
