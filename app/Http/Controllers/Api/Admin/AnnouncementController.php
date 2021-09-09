<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Admin\AnnouncementIndexRequest;
use App\Http\Requests\Api\Admin\AnnouncementShowRequest;
use App\Http\Resources\Admin\Collection\AnnouncementCollection;
use App\Http\Resources\Admin\Resource\AnnouncementResource;
use App\Models\Admin\Announcement;
use Illuminate\Http\JsonResponse;

/**
 * Class AnnouncementController.
 */
class AnnouncementController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param AnnouncementIndexRequest $request
     * @return JsonResponse
     */
    public function index(AnnouncementIndexRequest $request): JsonResponse
    {
        $announcements = AnnouncementCollection::performQuery($this->query);

        return $announcements->toResponse($request);
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
        $resource = AnnouncementResource::performQuery($announcement, $this->query);

        return $resource->toResponse($request);
    }
}
