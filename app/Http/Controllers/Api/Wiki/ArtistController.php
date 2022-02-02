<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Artist\ArtistIndexRequest;
use App\Http\Requests\Api\Wiki\Artist\ArtistShowRequest;
use App\Models\Wiki\Artist;
use Illuminate\Http\JsonResponse;

/**
 * Class ArtistController.
 */
class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  ArtistIndexRequest  $request
     * @return JsonResponse
     */
    public function index(ArtistIndexRequest $request): JsonResponse
    {
        $query = $request->getQuery();

        if ($query->hasSearchCriteria()) {
            return $query->search(PaginationStrategy::OFFSET())->toResponse($request);
        }

        return $query->index()->toResponse($request);
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
        $resource = $request->getQuery()->show($artist);

        return $resource->toResponse($request);
    }
}
