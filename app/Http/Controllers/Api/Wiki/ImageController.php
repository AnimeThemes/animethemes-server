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
use App\Http\Resources\Wiki\Resource\ImageJsonResource;
use App\Models\Wiki\Image;
use Illuminate\Http\JsonResponse;

class ImageController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Image::class, 'image');
    }

    public function index(IndexRequest $request, IndexAction $action): ImageCollection
    {
        $query = new Query($request->validated());

        $images = $action->index(Image::query(), $query, $request->schema());

        return new ImageCollection($images, $query);
    }

    public function store(StoreRequest $request, StoreImageAction $action): ImageJsonResource
    {
        $image = $action->store(Image::query(), $request->validated());

        return new ImageJsonResource($image, new Query());
    }

    public function show(ShowRequest $request, Image $image, ShowAction $action): ImageJsonResource
    {
        $query = new Query($request->validated());

        $show = $action->show($image, $query, $request->schema());

        return new ImageJsonResource($show, $query);
    }

    public function update(UpdateRequest $request, Image $image, UpdateAction $action): ImageJsonResource
    {
        $updated = $action->update($image, $request->validated());

        return new ImageJsonResource($updated, new Query());
    }

    public function destroy(Image $image, DestroyAction $action): ImageJsonResource
    {
        $deleted = $action->destroy($image);

        return new ImageJsonResource($deleted, new Query());
    }

    public function restore(Image $image, RestoreAction $action): ImageJsonResource
    {
        $restored = $action->restore($image);

        return new ImageJsonResource($restored, new Query());
    }

    public function forceDelete(Image $image, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($image);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
