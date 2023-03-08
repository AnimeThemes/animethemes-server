<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Pivot\Wiki;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\StoreAction;
use App\Actions\Http\Api\UpdateAction;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\Pivot\PivotController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\Pivot\Wiki\Collection\AnimeResourceCollection;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeResourceResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Illuminate\Http\JsonResponse;

/**
 * Class AnimeResourceController.
 */
class AnimeResourceController extends PivotController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Anime::class, 'anime', ExternalResource::class, 'resource');
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

        $resources = $action->index(AnimeResource::query(), $query, $request->schema());

        $collection = new AnimeResourceCollection($resources, $query);

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
        $animeResource = $action->store(AnimeResource::query(), $request->validated());

        $resource = new AnimeResourceResource($animeResource, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Anime  $anime
     * @param  ExternalResource  $resource
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Anime $anime, ExternalResource $resource, ShowAction $action): JsonResponse
    {
        $animeResource = AnimeResource::query()
            ->where(AnimeResource::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($animeResource, $query, $request->schema());

        $apiResource = new AnimeResourceResource($show, $query);

        return $apiResource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Anime  $anime
     * @param  ExternalResource  $resource
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Anime $anime, ExternalResource $resource, UpdateAction $action): JsonResponse
    {
        $animeResource = AnimeResource::query()
            ->where(AnimeResource::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $updated = $action->update($animeResource, $request->validated());

        $apiResource = new AnimeResourceResource($updated, $query);

        return $apiResource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Anime  $anime
     * @param  ExternalResource  $resource
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Anime $anime, ExternalResource $resource, DestroyAction $action): JsonResponse
    {
        $animeResource = AnimeResource::query()
            ->where(AnimeResource::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $action->destroy($animeResource);

        return new JsonResponse([
            'message' => "Resource '{$resource->getName()}' has been detached from Anime '{$anime->getName()}'.",
        ]);
    }
}
