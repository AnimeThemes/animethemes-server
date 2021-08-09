<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Models\Wiki\Artist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class ArtistController.
 */
class ArtistController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if ($this->query->hasSearchCriteria()) {
            return ArtistCollection::performSearch($this->query, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return ArtistCollection::performQuery($this->query)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Artist $artist
     * @return JsonResponse
     */
    public function show(Request $request, Artist $artist): JsonResponse
    {
        $resource = ArtistResource::performQuery($artist, $this->query);

        return $resource->toResponse($request);
    }
}
