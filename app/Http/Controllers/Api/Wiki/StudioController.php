<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Studio\StudioDestroyRequest;
use App\Http\Requests\Api\Wiki\Studio\StudioForceDeleteRequest;
use App\Http\Requests\Api\Wiki\Studio\StudioIndexRequest;
use App\Http\Requests\Api\Wiki\Studio\StudioRestoreRequest;
use App\Http\Requests\Api\Wiki\Studio\StudioShowRequest;
use App\Http\Requests\Api\Wiki\Studio\StudioStoreRequest;
use App\Http\Requests\Api\Wiki\Studio\StudioUpdateRequest;
use App\Models\Wiki\Studio;
use Illuminate\Http\JsonResponse;
use Spatie\RouteDiscovery\Attributes\Route;

/**
 * Class StudioController.
 */
class StudioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  StudioIndexRequest  $request
     * @return JsonResponse
     */
    #[Route(fullUri: 'studio', name: 'studio.index')]
    public function index(StudioIndexRequest $request): JsonResponse
    {
        $query = $request->getQuery();

        if ($query->hasSearchCriteria()) {
            return $query->search(PaginationStrategy::OFFSET())->toResponse($request);
        }

        return $query->index()->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StudioStoreRequest  $request
     * @return JsonResponse
     */
    #[Route(fullUri: 'studio', name: 'studio.store', middleware: 'auth:sanctum')]
    public function store(StudioStoreRequest $request): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  StudioShowRequest  $request
     * @param  Studio  $studio
     * @return JsonResponse
     */
    #[Route(fullUri: 'studio/{studio}', name: 'studio.show')]
    public function show(StudioShowRequest $request, Studio $studio): JsonResponse
    {
        $resource = $request->getQuery()->show($studio);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  StudioUpdateRequest  $request
     * @param  Studio  $studio
     * @return JsonResponse
     */
    #[Route(fullUri: 'studio/{studio}', name: 'studio.update', middleware: 'auth:sanctum')]
    public function update(StudioUpdateRequest $request, Studio $studio): JsonResponse
    {
        $resource = $request->getQuery()->update($studio);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  StudioDestroyRequest  $request
     * @param  Studio  $studio
     * @return JsonResponse
     */
    #[Route(fullUri: 'studio/{studio}', name: 'studio.destroy', middleware: 'auth:sanctum')]
    public function destroy(StudioDestroyRequest $request, Studio $studio): JsonResponse
    {
        $resource = $request->getQuery()->destroy($studio);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  StudioRestoreRequest  $request
     * @param  Studio  $studio
     * @return JsonResponse
     */
    #[Route(method: 'patch', fullUri: 'restore/studio/{studio}', name: 'studio.restore', middleware: 'auth:sanctum')]
    public function restore(StudioRestoreRequest $request, Studio $studio): JsonResponse
    {
        $resource = $request->getQuery()->restore($studio);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  StudioForceDeleteRequest  $request
     * @param  Studio  $studio
     * @return JsonResponse
     */
    #[Route(method: 'delete', fullUri: 'forceDelete/studio/{studio}', name: 'studio.forceDelete', middleware: 'auth:sanctum')]
    public function forceDelete(StudioForceDeleteRequest $request, Studio $studio): JsonResponse
    {
        return $request->getQuery()->forceDelete($studio);
    }
}
