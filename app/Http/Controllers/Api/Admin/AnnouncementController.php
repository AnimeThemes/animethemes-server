<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\AnnouncementIndexRequest;
use App\Http\Requests\Api\Admin\AnnouncementShowRequest;
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
     * @param  AnnouncementIndexRequest  $request
     * @return JsonResponse
     */
    public function index(AnnouncementIndexRequest $request): JsonResponse
    {
        $announcements = $request->getQuery()->index();

        return $announcements->toResponse($request);
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
}
