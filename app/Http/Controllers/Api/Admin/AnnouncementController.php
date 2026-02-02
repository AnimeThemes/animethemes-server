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
use App\Http\Resources\Admin\Resource\AnnouncementJsonResource;
use App\Models\Admin\Announcement;
use Illuminate\Http\JsonResponse;

class AnnouncementController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Announcement::class, 'announcement');
    }

    public function index(IndexRequest $request, IndexAction $action): AnnouncementCollection
    {
        $query = new Query($request->validated());

        /** @phpstan-ignore-next-line */
        $announcements = $action->index(Announcement::query()->current(), $query, $request->schema());

        return new AnnouncementCollection($announcements, $query);
    }

    /**
     * @param  StoreAction<Announcement>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): AnnouncementJsonResource
    {
        $announcement = $action->store(Announcement::query(), $request->validated());

        return new AnnouncementJsonResource($announcement, new Query());
    }

    public function show(ShowRequest $request, Announcement $announcement, ShowAction $action): AnnouncementJsonResource
    {
        $query = new Query($request->validated());

        $show = $action->show($announcement, $query, $request->schema());

        return new AnnouncementJsonResource($show, $query);
    }

    public function update(UpdateRequest $request, Announcement $announcement, UpdateAction $action): AnnouncementJsonResource
    {
        $updated = $action->update($announcement, $request->validated());

        return new AnnouncementJsonResource($updated, new Query());
    }

    public function destroy(Announcement $announcement, DestroyAction $action): JsonResponse
    {
        $message = $action->forceDelete($announcement);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
