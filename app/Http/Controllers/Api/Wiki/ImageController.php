<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Image\ImageIndexRequest;
use App\Http\Requests\Api\Wiki\Image\ImageShowRequest;
use App\Models\Wiki\Image;
use Illuminate\Http\JsonResponse;

/**
 * Class ImageController.
 */
class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  ImageIndexRequest  $request
     * @return JsonResponse
     */
    public function index(ImageIndexRequest $request): JsonResponse
    {
        $images = $request->getQuery()->index();

        return $images->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ImageShowRequest  $request
     * @param  Image  $image
     * @return JsonResponse
     */
    public function show(ImageShowRequest $request, Image $image): JsonResponse
    {
        $resource = $request->getQuery()->show($image);

        return $resource->toResponse($request);
    }
}
