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

/**
 * Class ArtistResourceController.
 */
class ArtistResourceController extends PivotController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Artist::class, 'artist', ExternalResource::class, 'resource');
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

        $resources = $action->index(ArtistResource::query(), $query, $request->schema());

        $collection = new ArtistResourceCollection($resources, $query);

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
        $artistResource = $action->store(ArtistResource::query(), $request->validated());

        $resource = new ArtistResourceResource($artistResource, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Artist  $artist
     * @param  ExternalResource  $resource
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Artist $artist, ExternalResource $resource, ShowAction $action): JsonResponse
    {
        $artistResource = ArtistResource::query()
            ->where(ArtistResource::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($artistResource, $query, $request->schema());

        $apiResource = new ArtistResourceResource($show, $query);

        return $apiResource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Artist  $artist
     * @param  ExternalResource  $resource
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Artist $artist, ExternalResource $resource, UpdateAction $action): JsonResponse
    {
        $artistResource = ArtistResource::query()
            ->where(ArtistResource::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $updated = $action->update($artistResource, $request->validated());

        $apiResource = new ArtistResourceResource($updated, $query);

        return $apiResource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Artist  $artist
     * @param  ExternalResource  $resource
     * @param  DestroyAction  $action
     * @return JsonResponse
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
