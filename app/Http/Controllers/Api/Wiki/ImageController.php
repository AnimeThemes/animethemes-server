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
     * @param Image $image
     * @return JsonResponse
     */
    public function show(Image $image): JsonResponse
    {
        $resource = ImageResource::performQuery($image, $this->parser);

        return $resource->toResponse(request());
    }
}
