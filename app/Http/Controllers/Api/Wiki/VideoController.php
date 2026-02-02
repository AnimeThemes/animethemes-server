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
use App\Http\Resources\Wiki\Resource\VideoJsonResource;
use App\Models\Wiki\Video;
use Illuminate\Http\JsonResponse;

class VideoController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Video::class, 'video');
    }

    public function index(IndexRequest $request, IndexAction $action): VideoCollection
    {
        $query = new Query($request->validated());

        $videos = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Video::query(), $query, $request->schema());

        return new VideoCollection($videos, $query);
    }

    /**
     * @param  StoreAction<Video>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): VideoJsonResource
    {
        $video = $action->store(Video::query(), $request->validated());

        return new VideoJsonResource($video, new Query());
    }

    public function show(ShowRequest $request, Video $video, ShowAction $action): VideoJsonResource
    {
        $query = new Query($request->validated());

        $show = $action->show($video, $query, $request->schema());

        return new VideoJsonResource($show, $query);
    }

    public function update(UpdateRequest $request, Video $video, UpdateAction $action): VideoJsonResource
    {
        $updated = $action->update($video, $request->validated());

        return new VideoJsonResource($updated, new Query());
    }

    public function destroy(Video $video, DestroyAction $action): VideoJsonResource
    {
        $deleted = $action->destroy($video);

        return new VideoJsonResource($deleted, new Query());
    }

    public function restore(Video $video, RestoreAction $action): VideoJsonResource
    {
        $restored = $action->restore($video);

        return new VideoJsonResource($restored, new Query());
    }

    public function forceDelete(Video $video, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($video);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
