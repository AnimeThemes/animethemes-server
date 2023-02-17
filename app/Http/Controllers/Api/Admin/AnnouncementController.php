<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

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
use App\Http\Resources\Admin\Collection\AnnouncementCollection;
use App\Http\Resources\Admin\Resource\AnnouncementResource;
use App\Models\Admin\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class AnnouncementController.
 */
class AnnouncementController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Announcement::class, 'announcement');
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

        $announcements = $action->index(Announcement::query(), $query, $request->schema());

        $collection = new AnnouncementCollection($announcements, $query);

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
        $announcement = $action->store(Announcement::query(), $request->validated());

        $resource = new AnnouncementResource($announcement, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Announcement  $announcement
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Announcement $announcement, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($announcement, $query, $request->schema());

        $resource = new AnnouncementResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Announcement  $announcement
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Announcement $announcement, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($announcement, $request->validated());

        $resource = new AnnouncementResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  Announcement  $announcement
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, Announcement $announcement, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($announcement);

        $resource = new AnnouncementResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  Announcement  $announcement
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, Announcement $announcement, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($announcement);

        $resource = new AnnouncementResource($restored, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  Announcement  $announcement
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(Announcement $announcement, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($announcement);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
