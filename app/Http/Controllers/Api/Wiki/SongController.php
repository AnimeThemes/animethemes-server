<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\Wiki\Song;
use Illuminate\Http\JsonResponse;

/**
 * Class SongController.
 */
class SongController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        if ($this->parser->hasSearch()) {
            return SongCollection::performSearch($this->parser, PaginationStrategy::OFFSET())->toResponse(request());
        }

        return SongCollection::performQuery($this->parser)->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @param Song $song
     * @return JsonResponse
     */
    public function show(Song $song): JsonResponse
    {
        $resource = SongResource::performQuery($song, $this->parser);

        return $resource->toResponse(request());
    }
}
