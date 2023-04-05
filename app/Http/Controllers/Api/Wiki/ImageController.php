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
     * @return ImageCollection
     */
    public function index(IndexRequest $request, IndexAction $action): ImageCollection
    {
        $query = new Query($request->validated());

        $images = $action->index(Image::query(), $query, $request->schema());

        return new ImageCollection($images, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreImageAction  $action
     * @return ImageResource
     */
    public function store(StoreRequest $request, StoreImageAction $action): ImageResource
    {
        $image = $action->store(Image::query(), $request->validated());

        return new ImageResource($image, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Image  $image
     * @param  ShowAction  $action
     * @return ImageResource
     */
    public function show(ShowRequest $request, Image $image, ShowAction $action): ImageResource
    {
        $query = new Query($request->validated());

        $show = $action->show($image, $query, $request->schema());

        return new ImageResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Image  $image
     * @param  UpdateAction  $action
     * @return ImageResource
     */
    public function update(UpdateRequest $request, Image $image, UpdateAction $action): ImageResource
    {
        $updated = $action->update($image, $request->validated());

        return new ImageResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  Image  $image
     * @param  DestroyAction  $action
     * @return ImageResource
     */
    public function destroy(Image $image, DestroyAction $action): ImageResource
    {
        $deleted = $action->destroy($image);

        return new ImageResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  Image  $image
     * @param  RestoreAction  $action
     * @return ImageResource
     */
    public function restore(Image $image, RestoreAction $action): ImageResource
    {
        $restored = $action->restore($image);

        return new ImageResource($restored, new Query());
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
