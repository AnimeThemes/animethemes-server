<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\AnnouncementCollection;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;

/**
 * Class AnnouncementController.
 */
class AnnouncementController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/announcement/",
     *     operationId="getAnnouncements",
     *     tags={"Announcement"},
     *     summary="Get paginated listing of Announcements",
     *     description="Returns listing of Announcements",
     *     @OA\Parameter(
     *         description="Sort announcement resource collection by fields. Case-insensitive options are announcement_id, created_at & updated_at.",
     *         example="sort=-updated_at",
     *         name="sort",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The number of resources to return per page. Acceptable range is [1-30]. Default value is 30.",
     *         example="page[size]=25",
     *         name="page[size]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="The page of resources to return.",
     *         example="page[number]=2",
     *         name="page[number]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[announcement]=content",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Property(property="announcements",type="array", @OA\Items(ref="#/components/schemas/AnnouncementResource")))
     *     )
     * )
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $announcements = AnnouncementCollection::performQuery($this->parser);

        return $announcements->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/announcement/{id}",
     *     operationId="getAnnouncement",
     *     tags={"Announcement"},
     *     summary="Get properties of Announcement",
     *     description="Returns properties of Announcement",
     *     @OA\Parameter(
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[announcement]=content",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(ref="#/components/schemas/AnnouncementResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found!"
     *     )
     * )
     *
     * @param Announcement $announcement
     * @return JsonResponse
     */
    public function show(Announcement $announcement): JsonResponse
    {
        $resource = AnnouncementResource::performQuery($announcement, $this->parser);

        return $resource->toResponse(request());
    }
}
