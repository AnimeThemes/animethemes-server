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
use Spatie\RouteDiscovery\Attributes\Route;

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
    #[Route(fullUri: 'resource', name: 'resource.index')]
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
    #[Route(fullUri: 'resource', name: 'resource.store', middleware: 'auth:sanctum')]
    public function store(ExternalResourceStoreRequest $request): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ExternalResourceShowRequest  $request
     * @param  ExternalResource  $resource
     * @return JsonResponse
     */
    #[Route(fullUri: 'resource/{resource}', name: 'resource.show')]
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
    #[Route(fullUri: 'resource/{resource}', name: 'resource.update', middleware: 'auth:sanctum')]
    public function update(ExternalResourceUpdateRequest $request, ExternalResource $resource): JsonResponse
    {
        $apiResource = $request->getQuery()->update($resource);

        return $apiResource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  ExternalResourceDestroyRequest  $request
     * @param  ExternalResource  $resource
     * @return JsonResponse
     */
    #[Route(fullUri: 'resource/{resource}', name: 'resource.destroy', middleware: 'auth:sanctum')]
    public function destroy(ExternalResourceDestroyRequest $request, ExternalResource $resource): JsonResponse
    {
        $apiResource = $request->getQuery()->destroy($resource);

        return $apiResource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  ExternalResourceRestoreRequest  $request
     * @param  ExternalResource  $resource
     * @return JsonResponse
     */
    #[Route(method: 'patch', fullUri: 'restore/resource/{resource}', name: 'resource.restore', middleware: 'auth:sanctum')]
    public function restore(ExternalResourceRestoreRequest $request, ExternalResource $resource): JsonResponse
    {
        $apiResource = $request->getQuery()->restore($resource);

        return $apiResource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  ExternalResourceForceDeleteRequest  $request
     * @param  ExternalResource  $resource
     * @return JsonResponse
     */
    #[Route(method: 'delete', fullUri: 'forceDelete/resource/{resource}', name: 'resource.forceDelete', middleware: 'auth:sanctum')]
    public function forceDelete(ExternalResourceForceDeleteRequest $request, ExternalResource $resource): JsonResponse
    {
        return $request->getQuery()->forceDelete($resource);
    }
}
