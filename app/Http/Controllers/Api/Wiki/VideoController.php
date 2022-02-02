<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Video\VideoIndexRequest;
use App\Http\Requests\Api\Wiki\Video\VideoShowRequest;
use App\Models\Wiki\Video;
use Illuminate\Http\JsonResponse;

/**
 * Class VideoController.
 */
class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  VideoIndexRequest  $request
     * @return JsonResponse
     */
    public function index(VideoIndexRequest $request): JsonResponse
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
     * @param  VideoShowRequest  $request
     * @param  Video  $video
     * @return JsonResponse
     */
    public function show(VideoShowRequest $request, Video $video): JsonResponse
    {
        $resource = $request->getQuery()->show($video);

        return $resource->toResponse($request);
    }
}
