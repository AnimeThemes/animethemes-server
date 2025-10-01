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

class ExternalResourceController extends BaseController
{
    public function __construct()
    {
        parent::__construct(ExternalResource::class, 'resource');
    }

    public function index(IndexRequest $request, IndexAction $action): ExternalResourceCollection
    {
        $query = new Query($request->validated());

        $resources = $action->index(ExternalResource::query(), $query, $request->schema());

        return new ExternalResourceCollection($resources, $query);
    }

    /**
     * @param  StoreAction<ExternalResource>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): ExternalResourceResource
    {
        $externalResource = $action->store(ExternalResource::query(), $request->validated());

        return new ExternalResourceResource($externalResource, new Query());
    }

    public function show(ShowRequest $request, ExternalResource $resource, ShowAction $action): ExternalResourceResource
    {
        $query = new Query($request->validated());

        $show = $action->show($resource, $query, $request->schema());

        return new ExternalResourceResource($show, $query);
    }

    public function update(UpdateRequest $request, ExternalResource $resource, UpdateAction $action): ExternalResourceResource
    {
        $updated = $action->update($resource, $request->validated());

        return new ExternalResourceResource($updated, new Query());
    }

    public function destroy(ExternalResource $resource, DestroyAction $action): ExternalResourceResource
    {
        $deleted = $action->destroy($resource);

        return new ExternalResourceResource($deleted, new Query());
    }

    public function restore(ExternalResource $resource, RestoreAction $action): ExternalResourceResource
    {
        $restored = $action->restore($resource);

        return new ExternalResourceResource($restored, new Query());
    }

    public function forceDelete(ExternalResource $resource, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($resource);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
