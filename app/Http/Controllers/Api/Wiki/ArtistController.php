<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Wiki\Artist\ArtistIndexRequest;
use App\Http\Requests\Api\Wiki\Artist\ArtistShowRequest;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Models\Wiki\Artist;
use Illuminate\Http\JsonResponse;

/**
 * Class ArtistController.
 */
class ArtistController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param  ArtistIndexRequest  $request
     * @return JsonResponse
     */
    public function index(ArtistIndexRequest $request): JsonResponse
    {
        if ($this->query->hasSearchCriteria()) {
            return ArtistCollection::performSearch($this->query, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return ArtistCollection::performQuery($this->query)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ArtistShowRequest  $request
     * @param  Artist  $artist
     * @return JsonResponse
     */
    public function show(ArtistShowRequest $request, Artist $artist): JsonResponse
    {
        $resource = ArtistResource::performQuery($artist, $this->query);

        return $resource->toResponse($request);
    }
}
