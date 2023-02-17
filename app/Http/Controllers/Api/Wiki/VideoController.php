<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\ForceDeleteAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\RestoreAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\StoreAction;
use App\Actions\Http\Api\UpdateAction;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
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
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Video::class, 'video');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $videos = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Video::query(), $query, $request->schema());

        $collection = new VideoCollection($videos, $query);

        return $collection->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction  $action
     * @return JsonResponse
     */
    public function store(StoreRequest $request, StoreAction $action): JsonResponse
    {
        $video = $action->store(Video::query(), $request->validated());

        $resource = new VideoResource($video, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Video  $video
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Video $video, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($video, $query, $request->schema());

        $resource = new VideoResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Video  $video
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Video $video, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($video, $request->validated());

        $resource = new VideoResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  Video  $video
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, Video $video, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($video);

        $resource = new VideoResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  Video  $video
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, Video $video, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($video);

        $resource = new VideoResource($restored, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  Video  $video
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(Video $video, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($video);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
