<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\ForceDeleteAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\RestoreAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\StoreAction;
use App\Actions\Http\Api\UpdateAction;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class ExternalResourceController.
 */
class ExternalResourceController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(ExternalResource::class, 'resource');
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

        $resources = $action->index(ExternalResource::query(), $query, $request->schema());

        $collection = new ExternalResourceCollection($resources, $query);

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
        $externalResource = $action->store(ExternalResource::query(), $request->validated());

        $resource = new ExternalResourceResource($externalResource, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  ExternalResource  $resource
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, ExternalResource $resource, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($resource, $query, $request->schema());

        $resource = new ExternalResourceResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  ExternalResource  $resource
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, ExternalResource $resource, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($resource, $request->validated());

        $apiResource = new ExternalResourceResource($updated, new Query());

        return $apiResource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  ExternalResource  $resource
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, ExternalResource $resource, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($resource);

        $apiResource = new ExternalResourceResource($deleted, new Query());

        return $apiResource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  ExternalResource  $resource
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, ExternalResource $resource, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($resource);

        $apiResource = new ExternalResourceResource($restored, new Query());

        return $apiResource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  ExternalResource  $resource
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(ExternalResource $resource, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($resource);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
