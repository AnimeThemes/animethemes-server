<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\ExternalResource\ExternalResourceDestroyRequest;
use App\Http\Requests\Api\Wiki\ExternalResource\ExternalResourceForceDeleteRequest;
use App\Http\Requests\Api\Wiki\ExternalResource\ExternalResourceIndexRequest;
use App\Http\Requests\Api\Wiki\ExternalResource\ExternalResourceRestoreRequest;
use App\Http\Requests\Api\Wiki\ExternalResource\ExternalResourceShowRequest;
use App\Http\Requests\Api\Wiki\ExternalResource\ExternalResourceStoreRequest;
use App\Http\Requests\Api\Wiki\ExternalResource\ExternalResourceUpdateRequest;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\JsonResponse;

/**
 * Class ExternalResourceController.
 */
class ExternalResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  ExternalResourceIndexRequest  $request
     * @return JsonResponse
     */
    public function index(ExternalResourceIndexRequest $request): JsonResponse
    {
        $resources = $request->getQuery()->index();

        return $resources->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  ExternalResourceStoreRequest  $request
     * @return JsonResponse
     */
    public function store(ExternalResourceStoreRequest $request): JsonResponse
    {
        $externalResource = ExternalResource::query()->create($request->validated());

        $resource = $request->getQuery()->resource($externalResource);

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ExternalResourceShowRequest  $request
     * @param  ExternalResource  $resource
     * @return JsonResponse
     */
    public function show(ExternalResourceShowRequest $request, ExternalResource $resource): JsonResponse
    {
        $apiResource = $request->getQuery()->show($resource);

        return $apiResource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  ExternalResourceUpdateRequest  $request
     * @param  ExternalResource  $resource
     * @return JsonResponse
     */
    public function update(ExternalResourceUpdateRequest $request, ExternalResource $resource): JsonResponse
    {
        $resource->update($request->validated());

        $apiResource = $request->getQuery()->resource($resource);

        return $apiResource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  ExternalResourceDestroyRequest  $request
     * @param  ExternalResource  $resource
     * @return JsonResponse
     */
    public function destroy(ExternalResourceDestroyRequest $request, ExternalResource $resource): JsonResponse
    {
        $resource->delete();

        $apiResource = $request->getQuery()->resource($resource);

        return $apiResource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  ExternalResourceRestoreRequest  $request
     * @param  ExternalResource  $resource
     * @return JsonResponse
     */
    public function restore(ExternalResourceRestoreRequest $request, ExternalResource $resource): JsonResponse
    {
        $resource->restore();

        $apiResource = $request->getQuery()->resource($resource);

        return $apiResource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  ExternalResourceForceDeleteRequest  $request
     * @param  ExternalResource  $resource
     * @return JsonResponse
     */
    public function forceDelete(ExternalResourceForceDeleteRequest $request, ExternalResource $resource): JsonResponse
    {
        $resource->forceDelete();

        $apiResource = $request->getQuery()->resource(new ExternalResource());

        return $apiResource->toResponse($request);
    }
}
