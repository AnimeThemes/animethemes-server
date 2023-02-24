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
use App\Http\Resources\Pivot\Wiki\Collection\ArtistImageCollection;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistImageResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\ArtistImage;
use Illuminate\Http\JsonResponse;

/**
 * Class ArtistImageController.
 */
class ArtistImageController extends PivotController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Artist::class, 'artist', Image::class, 'image');
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

        $resources = $action->index(ArtistImage::query(), $query, $request->schema());

        $collection = new ArtistImageCollection($resources, $query);

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
        $artistImage = $action->store(ArtistImage::query(), $request->validated());

        $resource = new ArtistImageResource($artistImage, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Artist  $artist
     * @param  Image  $image
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Artist $artist, Image $image, ShowAction $action): JsonResponse
    {
        $artistImage = ArtistImage::query()
            ->where(ArtistImage::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($artistImage, $query, $request->schema());

        $resource = new ArtistImageResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Artist  $artist
     * @param  Image  $image
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Artist $artist, Image $image, DestroyAction $action): JsonResponse
    {
        $artistImage = ArtistImage::query()
            ->where(ArtistImage::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->firstOrFail();

        $action->destroy($artistImage);

        return new JsonResponse([
            'message' => "Image '**{$image->getName()}**' has been detached from Artist '**{$artist->getName()}**'.",
        ]);
    }
}
