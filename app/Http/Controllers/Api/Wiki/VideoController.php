<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Video\VideoDestroyRequest;
use App\Http\Requests\Api\Wiki\Video\VideoForceDeleteRequest;
use App\Http\Requests\Api\Wiki\Video\VideoIndexRequest;
use App\Http\Requests\Api\Wiki\Video\VideoRestoreRequest;
use App\Http\Requests\Api\Wiki\Video\VideoShowRequest;
use App\Http\Requests\Api\Wiki\Video\VideoStoreRequest;
use App\Http\Requests\Api\Wiki\Video\VideoUpdateRequest;
use App\Models\Wiki\Video;
use Illuminate\Http\JsonResponse;
use Spatie\RouteDiscovery\Attributes\Route;

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
    #[Route(fullUri: 'video', name: 'video.index')]
    public function index(VideoIndexRequest $request): JsonResponse
    {
        $query = $request->getQuery();

        if ($query->hasSearchCriteria()) {
            return $query->search(PaginationStrategy::OFFSET())->toResponse($request);
        }

        return $query->index()->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  VideoStoreRequest  $request
     * @return JsonResponse
     */
    #[Route(fullUri: 'video', name: 'video.store', middleware: 'auth:sanctum')]
    public function store(VideoStoreRequest $request): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  VideoShowRequest  $request
     * @param  Video  $video
     * @return JsonResponse
     */
    #[Route(fullUri: 'video/{video}', name: 'video.show')]
    public function show(VideoShowRequest $request, Video $video): JsonResponse
    {
        $resource = $request->getQuery()->show($video);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  VideoUpdateRequest  $request
     * @param  Video  $video
     * @return JsonResponse
     */
    #[Route(fullUri: 'video/{video}', name: 'video.update', middleware: 'auth:sanctum')]
    public function update(VideoUpdateRequest $request, Video $video): JsonResponse
    {
        $resource = $request->getQuery()->update($video);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  VideoDestroyRequest  $request
     * @param  Video  $video
     * @return JsonResponse
     */
    #[Route(fullUri: 'video/{video}', name: 'video.destroy', middleware: 'auth:sanctum')]
    public function destroy(VideoDestroyRequest $request, Video $video): JsonResponse
    {
        $resource = $request->getQuery()->destroy($video);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  VideoRestoreRequest  $request
     * @param  Video  $video
     * @return JsonResponse
     */
    #[Route(method: 'patch', fullUri: 'restore/video/{video}', name: 'video.restore', middleware: 'auth:sanctum')]
    public function restore(VideoRestoreRequest $request, Video $video): JsonResponse
    {
        $resource = $request->getQuery()->restore($video);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  VideoForceDeleteRequest  $request
     * @param  Video  $video
     * @return JsonResponse
     */
    #[Route(method: 'delete', fullUri: 'forceDelete/video/{video}', name: 'video.forceDelete', middleware: 'auth:sanctum')]
    public function forceDelete(VideoForceDeleteRequest $request, Video $video): JsonResponse
    {
        return $request->getQuery()->forceDelete($video);
    }
}
