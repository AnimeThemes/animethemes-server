<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\ForceDeleteAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\RestoreAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\UpdateAction;
use App\Actions\Http\Api\Wiki\Image\StoreImageAction;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
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
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Image::class, 'image');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $images = $action->index(Image::query(), $query, $request->schema());

        $collection = new ImageCollection($images, $query);

        return $collection->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreImageAction  $action
     * @return JsonResponse
     */
    public function store(StoreRequest $request, StoreImageAction $action): JsonResponse
    {
        $image = $action->store(Image::query(), $request->validated());

        $resource = new ImageResource($image, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Image  $image
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Image $image, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($image, $query, $request->schema());

        $resource = new ImageResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Image  $image
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Image $image, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($image, $request->validated());

        $resource = new ImageResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  Image  $image
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, Image $image, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($image);

        $resource = new ImageResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  Image  $image
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, Image $image, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($image);

        $resource = new ImageResource($restored, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  Image  $image
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(Image $image, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($image);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
