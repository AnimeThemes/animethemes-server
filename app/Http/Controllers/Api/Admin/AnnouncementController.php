<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
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
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $announcements = AnnouncementCollection::performQuery($this->parser);

        return $announcements->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Announcement $announcement
     * @return JsonResponse
     */
    public function show(Request $request, Announcement $announcement): JsonResponse
    {
        $resource = AnnouncementResource::performQuery($announcement, $this->parser);

        return $resource->toResponse($request);
    }
}
