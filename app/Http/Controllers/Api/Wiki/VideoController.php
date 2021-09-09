<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Wiki\Video\VideoIndexRequest;
use App\Http\Requests\Api\Wiki\Video\VideoShowRequest;
use App\Http\Resources\Wiki\Collection\VideoCollection;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\Wiki\Video;
use Illuminate\Http\JsonResponse;

/**
 * Class VideoController.
 */
class VideoController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param VideoIndexRequest $request
     * @return JsonResponse
     */
    public function index(VideoIndexRequest $request): JsonResponse
    {
        if ($this->query->hasSearchCriteria()) {
            return VideoCollection::performSearch($this->query, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return VideoCollection::performQuery($this->query)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param VideoShowRequest $request
     * @param Video $video
     * @return JsonResponse
     */
    public function show(VideoShowRequest $request, Video $video): JsonResponse
    {
        $resource = VideoResource::performQuery($video, $this->query);

        return $resource->toResponse($request);
    }
}
