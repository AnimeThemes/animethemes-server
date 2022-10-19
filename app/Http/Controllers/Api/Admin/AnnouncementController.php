<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Admin\Announcement\AnnouncementDestroyRequest;
use App\Http\Requests\Api\Admin\Announcement\AnnouncementForceDeleteRequest;
use App\Http\Requests\Api\Admin\Announcement\AnnouncementIndexRequest;
use App\Http\Requests\Api\Admin\Announcement\AnnouncementRestoreRequest;
use App\Http\Requests\Api\Admin\Announcement\AnnouncementShowRequest;
use App\Http\Requests\Api\Admin\Announcement\AnnouncementStoreRequest;
use App\Http\Requests\Api\Admin\Announcement\AnnouncementUpdateRequest;
use App\Models\Admin\Announcement;
use Illuminate\Http\JsonResponse;

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
     * @param  AnnouncementIndexRequest  $request
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
     * @param  AnnouncementStoreRequest  $request
     * @return JsonResponse
     */
    public function store(AnnouncementStoreRequest $request): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  AnnouncementShowRequest  $request
     * @param  Announcement  $announcement
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
     * @param  AnnouncementUpdateRequest  $request
     * @param  Announcement  $announcement
     * @return JsonResponse
     */
    public function update(AnnouncementUpdateRequest $request, Announcement $announcement): JsonResponse
    {
        $resource = $request->getQuery()->update($announcement);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  AnnouncementDestroyRequest  $request
     * @param  Announcement  $announcement
     * @return JsonResponse
     */
    public function destroy(AnnouncementDestroyRequest $request, Announcement $announcement): JsonResponse
    {
        $resource = $request->getQuery()->destroy($announcement);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  AnnouncementRestoreRequest  $request
     * @param  Announcement  $announcement
     * @return JsonResponse
     */
    public function restore(AnnouncementRestoreRequest $request, Announcement $announcement): JsonResponse
    {
        $resource = $request->getQuery()->restore($announcement);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  AnnouncementForceDeleteRequest  $request
     * @param  Announcement  $announcement
     * @return JsonResponse
     */
    public function forceDelete(AnnouncementForceDeleteRequest $request, Announcement $announcement): JsonResponse
    {
        return $request->getQuery()->forceDelete($announcement);
    }
}
