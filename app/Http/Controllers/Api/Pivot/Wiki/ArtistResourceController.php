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
use App\Http\Resources\Pivot\Wiki\Collection\ArtistResourceCollection;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistResourceResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\ArtistResource;
use Illuminate\Http\JsonResponse;

class ArtistResourceController extends PivotController
{
    public function __construct()
    {
        parent::__construct(Artist::class, 'artist', ExternalResource::class, 'resource');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexAction  $action
     */
    public function index(IndexRequest $request, IndexAction $action): ArtistResourceCollection
    {
        $query = new Query($request->validated());

        $resources = $action->index(ArtistResource::query(), $query, $request->schema());

        return new ArtistResourceCollection($resources, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreAction<ArtistResource>  $action
     */
    public function store(StoreRequest $request, Artist $artist, ExternalResource $resource, StoreAction $action): ArtistResourceResource
    {
        $validated = array_merge(
            $request->validated(),
            [
                ArtistResource::ATTRIBUTE_ARTIST => $artist->getKey(),
                ArtistResource::ATTRIBUTE_RESOURCE => $resource->getKey(),
            ]
        );

        $artistResource = $action->store(ArtistResource::query(), $validated);

        return new ArtistResourceResource($artistResource, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowAction  $action
     */
    public function show(ShowRequest $request, Artist $artist, ExternalResource $resource, ShowAction $action): ArtistResourceResource
    {
        $artistResource = ArtistResource::query()
            ->where(ArtistResource::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($artistResource, $query, $request->schema());

        return new ArtistResourceResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateAction  $action
     */
    public function update(UpdateRequest $request, Artist $artist, ExternalResource $resource, UpdateAction $action): ArtistResourceResource
    {
        $artistResource = ArtistResource::query()
            ->where(ArtistResource::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $updated = $action->update($artistResource, $request->validated());

        return new ArtistResourceResource($updated, $query);
    }

    /**
     * Remove the specified resource.
     *
     * @param  DestroyAction  $action
     */
    public function destroy(Artist $artist, ExternalResource $resource, DestroyAction $action): JsonResponse
    {
        $artistResource = ArtistResource::query()
            ->where(ArtistResource::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $action->destroy($artistResource);

        return new JsonResponse([
            'message' => "Resource '{$resource->getName()}' has been detached from Artist '{$artist->getName()}'.",
        ]);
    }
}
