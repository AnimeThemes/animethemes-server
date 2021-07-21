<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Collection\VideoCollection;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\Wiki\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class VideoController.
 */
class VideoController extends BaseController
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
            return VideoCollection::performSearch($this->parser, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return VideoCollection::performQuery($this->parser)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Video $video
     * @return JsonResponse
     */
    public function show(Request $request, Video $video): JsonResponse
    {
        $resource = VideoResource::performQuery($video, $this->parser);

        return $resource->toResponse($request);
    }
}
