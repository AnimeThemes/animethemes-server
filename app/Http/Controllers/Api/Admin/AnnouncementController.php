<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\IndexAction;
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
     * @return AnnouncementCollection
     */
    public function index(IndexRequest $request, IndexAction $action): AnnouncementCollection
    {
        $query = new Query($request->validated());

        /** @phpstan-ignore-next-line */
        $announcements = $action->index(Announcement::query()->public(), $query, $request->schema());

        return new AnnouncementCollection($announcements, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction<Announcement>  $action
     * @return AnnouncementResource
     */
    public function store(StoreRequest $request, StoreAction $action): AnnouncementResource
    {
        $announcement = $action->store(Announcement::query(), $request->validated());

        return new AnnouncementResource($announcement, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Announcement  $announcement
     * @param  ShowAction  $action
     * @return AnnouncementResource
     */
    public function show(ShowRequest $request, Announcement $announcement, ShowAction $action): AnnouncementResource
    {
        $query = new Query($request->validated());

        $show = $action->show($announcement, $query, $request->schema());

        return new AnnouncementResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Announcement  $announcement
     * @param  UpdateAction  $action
     * @return AnnouncementResource
     */
    public function update(UpdateRequest $request, Announcement $announcement, UpdateAction $action): AnnouncementResource
    {
        $updated = $action->update($announcement, $request->validated());

        return new AnnouncementResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  Announcement  $announcement
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Announcement $announcement, DestroyAction $action): JsonResponse
    {
        $message = $action->forceDelete($announcement);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
