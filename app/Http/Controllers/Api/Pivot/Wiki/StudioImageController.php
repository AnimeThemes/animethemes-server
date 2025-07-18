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
     * @return StudioImageCollection
     */
    public function index(IndexRequest $request, IndexAction $action): StudioImageCollection
    {
        $query = new Query($request->validated());

        $resources = $action->index(StudioImage::query(), $query, $request->schema());

        return new StudioImageCollection($resources, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  Studio  $studio
     * @param  Image  $image
     * @param  StoreAction<StudioImage>  $action
     * @return StudioImageResource
     */
    public function store(StoreRequest $request, Studio $studio, Image $image, StoreAction $action): StudioImageResource
    {
        $validated = array_merge(
            $request->validated(),
            [
                StudioImage::ATTRIBUTE_STUDIO => $studio->getKey(),
                StudioImage::ATTRIBUTE_IMAGE => $image->getKey(),
            ]
        );

        $studioImage = $action->store(StudioImage::query(), $validated);

        return new StudioImageResource($studioImage, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Studio  $studio
     * @param  Image  $image
     * @param  ShowAction  $action
     * @return StudioImageResource
     */
    public function show(ShowRequest $request, Studio $studio, Image $image, ShowAction $action): StudioImageResource
    {
        $studioImage = StudioImage::query()
            ->where(StudioImage::ATTRIBUTE_STUDIO, $studio->getKey())
            ->where(StudioImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($studioImage, $query, $request->schema());

        return new StudioImageResource($show, $query);
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
