<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Collection\ImageCollection;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Models\Wiki\Image;
use Illuminate\Http\JsonResponse;

/**
 * Class ImageController.
 */
class ImageController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/image/",
     *     operationId="getImages",
     *     tags={"Image"},
     *     summary="Get paginated listing of Images",
     *     description="Returns listing of Images",
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is anime & artists.",
     *         example="include=anime",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Filter images by facet. Case-insensitive options are COVER_SMALL & COVER_LARGE.",
     *         example="filter[facet]=COVER_SMALL",
     *         name="facet",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Sort image collection by fields. Case-insensitive options are image_id, created_at, updated_at, path & facet.",
     *         example="sort=-updated_at,image_id",
     *         name="sort",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The number of images to return per page. Acceptable range is [1-30]. Default value is 30.",
     *         example="page[size]=25",
     *         name="page[size]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="The page of images to return.",
     *         example="page[number]=2",
     *         name="page[number]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[image]=image_id,link",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Property(property="images",type="array", @OA\Items(ref="#/components/schemas/ImageResource")))
     *     )
     * )
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $images = ImageCollection::performQuery($this->parser);

        return $images->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/image/{id}",
     *     operationId="getImage",
     *     tags={"Image"},
     *     summary="Get properties of Image",
     *     description="Returns properties of Image",
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is anime & artists.",
     *         example="include=anime",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[image]=image_id,link",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(ref="#/components/schemas/ImageResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found!"
     *     )
     * )
     *
     * @param Image $image
     * @return JsonResponse
     */
    public function show(Image $image): JsonResponse
    {
        $resource = ImageResource::performQuery($image, $this->parser);

        return $resource->toResponse(request());
    }
}
