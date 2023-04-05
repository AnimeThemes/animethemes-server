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
     * @return ArtistImageCollection
     */
    public function index(IndexRequest $request, IndexAction $action): ArtistImageCollection
    {
        $query = new Query($request->validated());

        $resources = $action->index(ArtistImage::query(), $query, $request->schema());

        return new ArtistImageCollection($resources, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  Artist  $artist
     * @param  Image  $image
     * @param  StoreAction  $action
     * @return ArtistImageResource
     */
    public function store(StoreRequest $request, Artist $artist, Image $image, StoreAction $action): ArtistImageResource
    {
        $validated = array_merge(
            $request->validated(),
            [
                ArtistImage::ATTRIBUTE_ARTIST => $artist->getKey(),
                ArtistImage::ATTRIBUTE_IMAGE => $image->getKey(),
            ]
        );

        $artistImage = $action->store(ArtistImage::query(), $validated);

        return new ArtistImageResource($artistImage, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Artist  $artist
     * @param  Image  $image
     * @param  ShowAction  $action
     * @return ArtistImageResource
     */
    public function show(ShowRequest $request, Artist $artist, Image $image, ShowAction $action): ArtistImageResource
    {
        $artistImage = ArtistImage::query()
            ->where(ArtistImage::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($artistImage, $query, $request->schema());

        return new ArtistImageResource($show, $query);
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
            'message' => "Image '{$image->getName()}' has been detached from Artist '{$artist->getName()}'.",
        ]);
    }
}
