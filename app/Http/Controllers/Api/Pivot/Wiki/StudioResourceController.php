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
use App\Http\Resources\Pivot\Wiki\Collection\StudioResourceCollection;
use App\Http\Resources\Pivot\Wiki\Resource\StudioResourceResource;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Http\JsonResponse;

/**
 * Class StudioResourceController.
 */
class StudioResourceController extends PivotController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Studio::class, 'studio', ExternalResource::class, 'resource');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return StudioResourceCollection
     */
    public function index(IndexRequest $request, IndexAction $action): StudioResourceCollection
    {
        $query = new Query($request->validated());

        $resources = $action->index(StudioResource::query(), $query, $request->schema());

        return new StudioResourceCollection($resources, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  Studio  $studio
     * @param  ExternalResource  $resource
     * @param  StoreAction  $action
     * @return StudioResourceResource
     */
    public function store(StoreRequest $request, Studio $studio, ExternalResource $resource, StoreAction $action): StudioResourceResource
    {
        $validated = array_merge(
            $request->validated(),
            [
                StudioResource::ATTRIBUTE_STUDIO => $studio->getKey(),
                StudioResource::ATTRIBUTE_RESOURCE => $resource->getKey(),
            ]
        );

        $studioResource = $action->store(StudioResource::query(), $validated);

        return new StudioResourceResource($studioResource, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Studio  $studio
     * @param  ExternalResource  $resource
     * @param  ShowAction  $action
     * @return StudioResourceResource
     */
    public function show(ShowRequest $request, Studio $studio, ExternalResource $resource, ShowAction $action): StudioResourceResource
    {
        $studioResource = StudioResource::query()
            ->where(StudioResource::ATTRIBUTE_STUDIO, $studio->getKey())
            ->where(StudioResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($studioResource, $query, $request->schema());

        return new StudioResourceResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Studio  $studio
     * @param  ExternalResource  $resource
     * @param  UpdateAction  $action
     * @return StudioResourceResource
     */
    public function update(UpdateRequest $request, Studio $studio, ExternalResource $resource, UpdateAction $action): StudioResourceResource
    {
        $studioResource = StudioResource::query()
            ->where(StudioResource::ATTRIBUTE_STUDIO, $studio->getKey())
            ->where(StudioResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $updated = $action->update($studioResource, $request->validated());

        return new StudioResourceResource($updated, $query);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Studio  $studio
     * @param  ExternalResource  $resource
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Studio $studio, ExternalResource $resource, DestroyAction $action): JsonResponse
    {
        $studioResource = StudioResource::query()
            ->where(StudioResource::ATTRIBUTE_STUDIO, $studio->getKey())
            ->where(StudioResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $action->destroy($studioResource);

        return new JsonResponse([
            'message' => "Resource '{$resource->getName()}' has been detached from Studio '{$studio->getName()}'.",
        ]);
    }
}
