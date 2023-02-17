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
use App\Http\Resources\Pivot\Wiki\Collection\AnimeImageCollection;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeImageResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\AnimeImage;
use Illuminate\Http\JsonResponse;

/**
 * Class AnimeImageController.
 */
class AnimeImageController extends PivotController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Anime::class, 'anime', Image::class, 'image');
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

        $resources = $action->index(AnimeImage::query(), $query, $request->schema());

        $collection = new AnimeImageCollection($resources, $query);

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
        $animeImage = $action->store(AnimeImage::query(), $request->validated());

        $resource = new AnimeImageResource($animeImage, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Anime  $anime
     * @param  Image  $image
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Anime $anime, Image $image, ShowAction $action): JsonResponse
    {
        $animeImage = AnimeImage::query()
            ->where(AnimeImage::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($animeImage, $query, $request->schema());

        $resource = new AnimeImageResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Anime  $anime
     * @param  Image  $image
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Anime $anime, Image $image, DestroyAction $action): JsonResponse
    {
        $animeImage = AnimeImage::query()
            ->where(AnimeImage::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->firstOrFail();

        $action->destroy($animeImage);

        return new JsonResponse([
            'message' => "Image '**{$image->getName()}**' has been detached from Anime '**{$anime->getName()}**'.",
        ]);
    }
}
