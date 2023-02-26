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
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $resources = $action->index(StudioResource::query(), $query, $request->schema());

        $collection = new StudioResourceCollection($resources, $query);

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
        $studioResource = $action->store(StudioResource::query(), $request->validated());

        $resource = new StudioResourceResource($studioResource, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Studio  $studio
     * @param  ExternalResource  $resource
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Studio $studio, ExternalResource $resource, ShowAction $action): JsonResponse
    {
        $studioResource = StudioResource::query()
            ->where(StudioResource::ATTRIBUTE_STUDIO, $studio->getKey())
            ->where(StudioResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($studioResource, $query, $request->schema());

        $apiResource = new StudioResourceResource($show, $query);

        return $apiResource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Studio  $studio
     * @param  ExternalResource  $resource
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Studio $studio, ExternalResource $resource, UpdateAction $action): JsonResponse
    {
        $studioResource = StudioResource::query()
            ->where(StudioResource::ATTRIBUTE_STUDIO, $studio->getKey())
            ->where(StudioResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $updated = $action->update($studioResource, $request->validated());

        $apiResource = new StudioResourceResource($updated, $query);

        return $apiResource->toResponse($request);
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
            'message' => "Resource '**{$resource->getName()}**' has been detached from Studio '**{$studio->getName()}**'.",
        ]);
    }
}
