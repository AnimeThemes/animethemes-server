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
     * @return AnimeResourceCollection
     */
    public function index(IndexRequest $request, IndexAction $action): AnimeResourceCollection
    {
        $query = new Query($request->validated());

        $resources = $action->index(AnimeResource::query(), $query, $request->schema());

        return new AnimeResourceCollection($resources, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  Anime  $anime
     * @param  ExternalResource  $resource
     * @param  StoreAction<AnimeResource>  $action
     * @return AnimeResourceResource
     */
    public function store(StoreRequest $request, Anime $anime, ExternalResource $resource, StoreAction $action): AnimeResourceResource
    {
        $validated = array_merge(
            $request->validated(),
            [
                AnimeResource::ATTRIBUTE_ANIME => $anime->getKey(),
                AnimeResource::ATTRIBUTE_RESOURCE => $resource->getKey(),
            ]
        );

        $animeResource = $action->store(AnimeResource::query(), $validated);

        return new AnimeResourceResource($animeResource, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Anime  $anime
     * @param  ExternalResource  $resource
     * @param  ShowAction  $action
     * @return AnimeResourceResource
     */
    public function show(ShowRequest $request, Anime $anime, ExternalResource $resource, ShowAction $action): AnimeResourceResource
    {
        $animeResource = AnimeResource::query()
            ->where(AnimeResource::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($animeResource, $query, $request->schema());

        return new AnimeResourceResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Anime  $anime
     * @param  ExternalResource  $resource
     * @param  UpdateAction  $action
     * @return AnimeResourceResource
     */
    public function update(UpdateRequest $request, Anime $anime, ExternalResource $resource, UpdateAction $action): AnimeResourceResource
    {
        $animeResource = AnimeResource::query()
            ->where(AnimeResource::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $updated = $action->update($animeResource, $request->validated());

        return new AnimeResourceResource($updated, $query);
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
