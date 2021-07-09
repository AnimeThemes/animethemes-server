<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
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
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        if ($this->parser->hasSearch()) {
            return ArtistCollection::performSearch($this->parser, PaginationStrategy::OFFSET())->toResponse(request());
        }

        return ArtistCollection::performQuery($this->parser)->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @param Artist $artist
     * @return JsonResponse
     */
    public function show(Artist $artist): JsonResponse
    {
        $resource = ArtistResource::performQuery($artist, $this->parser);

        return $resource->toResponse(request());
    }
}