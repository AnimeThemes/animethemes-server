<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\Wiki\Song;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class SongController.
 */
class SongController extends BaseController
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
            return SongCollection::performSearch($this->parser, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return SongCollection::performQuery($this->parser)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Song $song
     * @return JsonResponse
     */
    public function show(Request $request, Song $song): JsonResponse
    {
        $resource = SongResource::performQuery($song, $this->parser);

        return $resource->toResponse($request);
    }
}
