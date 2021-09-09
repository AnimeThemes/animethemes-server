<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Wiki\Image\ImageIndexRequest;
use App\Http\Requests\Api\Wiki\Image\ImageShowRequest;
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
     * @param ImageIndexRequest $request
     * @return JsonResponse
     */
    public function index(ImageIndexRequest $request): JsonResponse
    {
        $images = ImageCollection::performQuery($this->query);

        return $images->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param ImageShowRequest $request
     * @param Image $image
     * @return JsonResponse
     */
    public function show(ImageShowRequest $request, Image $image): JsonResponse
    {
        $resource = ImageResource::performQuery($image, $this->query);

        return $resource->toResponse($request);
    }
}
