<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Artist\ArtistDestroyRequest;
use App\Http\Requests\Api\Wiki\Artist\ArtistForceDeleteRequest;
use App\Http\Requests\Api\Wiki\Artist\ArtistIndexRequest;
use App\Http\Requests\Api\Wiki\Artist\ArtistRestoreRequest;
use App\Http\Requests\Api\Wiki\Artist\ArtistShowRequest;
use App\Http\Requests\Api\Wiki\Artist\ArtistStoreRequest;
use App\Http\Requests\Api\Wiki\Artist\ArtistUpdateRequest;
use App\Models\Wiki\Artist;
use Illuminate\Http\JsonResponse;
use Spatie\RouteDiscovery\Attributes\Route;

/**
 * Class ArtistController.
 */
class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  ArtistIndexRequest  $request
     * @return JsonResponse
     */
    #[Route(fullUri: 'artist', name: 'artist.index')]
    public function index(ArtistIndexRequest $request): JsonResponse
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
     * @param  ArtistStoreRequest  $request
     * @return JsonResponse
     */
    #[Route(fullUri: 'artist', name: 'artist.store', middleware: 'auth:sanctum')]
    public function store(ArtistStoreRequest $request): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ArtistShowRequest  $request
     * @param  Artist  $artist
     * @return JsonResponse
     */
    #[Route(fullUri: 'artist/{artist}', name: 'artist.show')]
    public function show(ArtistShowRequest $request, Artist $artist): JsonResponse
    {
        $resource = $request->getQuery()->show($artist);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  ArtistUpdateRequest  $request
     * @param  Artist  $artist
     * @return JsonResponse
     */
    #[Route(fullUri: 'artist/{artist}', name: 'artist.update', middleware: 'auth:sanctum')]
    public function update(ArtistUpdateRequest $request, Artist $artist): JsonResponse
    {
        $resource = $request->getQuery()->update($artist);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  ArtistDestroyRequest  $request
     * @param  Artist  $artist
     * @return JsonResponse
     */
    #[Route(fullUri: 'artist/{artist}', name: 'artist.destroy', middleware: 'auth:sanctum')]
    public function destroy(ArtistDestroyRequest $request, Artist $artist): JsonResponse
    {
        $resource = $request->getQuery()->destroy($artist);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  ArtistRestoreRequest  $request
     * @param  Artist  $artist
     * @return JsonResponse
     */
    #[Route(method: 'patch', fullUri: 'restore/artist/{artist}', name: 'artist.restore', middleware: 'auth:sanctum')]
    public function restore(ArtistRestoreRequest $request, Artist $artist): JsonResponse
    {
        $resource = $request->getQuery()->restore($artist);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  ArtistForceDeleteRequest  $request
     * @param  Artist  $artist
     * @return JsonResponse
     */
    #[Route(method: 'delete', fullUri: 'forceDelete/artist/{artist}', name: 'artist.forceDelete', middleware: 'auth:sanctum')]
    public function forceDelete(ArtistForceDeleteRequest $request, Artist $artist): JsonResponse
    {
        return $request->getQuery()->forceDelete($artist);
    }
}
