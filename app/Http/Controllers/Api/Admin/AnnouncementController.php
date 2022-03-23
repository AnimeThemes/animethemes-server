<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\AnnouncementDestroyRequest;
use App\Http\Requests\Api\Admin\AnnouncementForceDeleteRequest;
use App\Http\Requests\Api\Admin\AnnouncementIndexRequest;
use App\Http\Requests\Api\Admin\AnnouncementRestoreRequest;
use App\Http\Requests\Api\Admin\AnnouncementShowRequest;
use App\Http\Requests\Api\Admin\AnnouncementStoreRequest;
use App\Http\Requests\Api\Admin\AnnouncementUpdateRequest;
use App\Models\Admin\Announcement;
use Illuminate\Http\JsonResponse;

/**
 * Class AnnouncementController.
 */
class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param AnnouncementIndexRequest $request
     * @return JsonResponse
     */
    public function index(AnnouncementIndexRequest $request): JsonResponse
    {
        $announcements = $request->getQuery()->index();

        return $announcements->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param AnnouncementStoreRequest $request
     * @return JsonResponse
     */
    public function store(AnnouncementStoreRequest $request): JsonResponse
    {
        $announcement = Announcement::query()->create($request->validated());

        $resource = $request->getQuery()->resource($announcement);

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param AnnouncementShowRequest $request
     * @param Announcement $announcement
     * @return JsonResponse
     */
    public function show(AnnouncementShowRequest $request, Announcement $announcement): JsonResponse
    {
        $resource = $request->getQuery()->show($announcement);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param AnnouncementUpdateRequest $request
     * @param Announcement $announcement
     * @return JsonResponse
     */
    public function update(AnnouncementUpdateRequest $request, Announcement $announcement): JsonResponse
    {
        $announcement->update($request->validated());

        $resource = $request->getQuery()->resource($announcement);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param AnnouncementDestroyRequest $request
     * @param Announcement $announcement
     * @return JsonResponse
     */
    public function destroy(AnnouncementDestroyRequest $request, Announcement $announcement): JsonResponse
    {
        $announcement->delete();

        $resource = $request->getQuery()->resource($announcement);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param AnnouncementRestoreRequest $request
     * @param Announcement $announcement
     * @return JsonResponse
     */
    public function restore(AnnouncementRestoreRequest $request, Announcement $announcement): JsonResponse
    {
        $announcement->restore();

        $resource = $request->getQuery()->resource($announcement);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param AnnouncementForceDeleteRequest $request
     * @param Announcement $announcement
     * @return JsonResponse
     */
    public function forceDelete(AnnouncementForceDeleteRequest $request, Announcement $announcement): JsonResponse
    {
        $announcement->forceDelete();

        $resource = $request->getQuery()->resource(new Announcement());

        return $resource->toResponse($request);
    }
}
