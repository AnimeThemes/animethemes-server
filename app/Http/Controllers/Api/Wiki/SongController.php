<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Song\SongIndexRequest;
use App\Http\Requests\Api\Wiki\Song\SongShowRequest;
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\Wiki\Song;
use Illuminate\Http\JsonResponse;

/**
 * Class SongController.
 */
class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  SongIndexRequest  $request
     * @return JsonResponse
     */
    public function index(SongIndexRequest $request): JsonResponse
    {
        $query = $request->getQuery();

        if ($query->hasSearchCriteria()) {
            return SongCollection::performSearch($query, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return SongCollection::performQuery($query)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  SongShowRequest  $request
     * @param  Song  $song
     * @return JsonResponse
     */
    public function show(SongShowRequest $request, Song $song): JsonResponse
    {
        $resource = SongResource::performQuery($song, $request->getQuery());

        return $resource->toResponse($request);
    }
}
