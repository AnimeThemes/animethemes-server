<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Pivot\Wiki;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\StoreAction;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\Pivot\PivotController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Resources\Pivot\Wiki\Collection\StudioImageCollection;
use App\Http\Resources\Pivot\Wiki\Resource\StudioImageResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioImage;
use Illuminate\Http\JsonResponse;

/**
 * Class StudioImageController.
 */
class StudioImageController extends PivotController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Studio::class, 'studio', Image::class, 'image');
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

        $resources = $action->index(StudioImage::query(), $query, $request->schema());

        $collection = new StudioImageCollection($resources, $query);

        return $collection->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction  $action
     * @return JsonResponse
     */
    public function store(StoreRequest $request, StoreAction $action): JsonResponse
    {
        $studioImage = $action->store(StudioImage::query(), $request->validated());

        $resource = new StudioImageResource($studioImage, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Studio  $studio
     * @param  Image  $image
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Studio $studio, Image $image, ShowAction $action): JsonResponse
    {
        $studioImage = StudioImage::query()
            ->where(StudioImage::ATTRIBUTE_STUDIO, $studio->getKey())
            ->where(StudioImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($studioImage, $query, $request->schema());

        $resource = new StudioImageResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Studio  $studio
     * @param  Image  $image
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Studio $studio, Image $image, DestroyAction $action): JsonResponse
    {
        $studioImage = StudioImage::query()
            ->where(StudioImage::ATTRIBUTE_STUDIO, $studio->getKey())
            ->where(StudioImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->firstOrFail();

        $action->destroy($studioImage);

        return new JsonResponse([
            'message' => "Image '{$image->getName()}' has been detached from Studio '{$studio->getName()}'.",
        ]);
    }
}
