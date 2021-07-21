<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Collection\ImageCollection;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Models\Wiki\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class ImageController.
 */
class ImageController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $images = ImageCollection::performQuery($this->parser);

        return $images->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Image $image
     * @return JsonResponse
     */
    public function show(Request $request, Image $image): JsonResponse
    {
        $resource = ImageResource::performQuery($image, $this->parser);

        return $resource->toResponse($request);
    }
}
