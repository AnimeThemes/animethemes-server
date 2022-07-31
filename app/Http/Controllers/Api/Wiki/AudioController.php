<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Audio\AudioDestroyRequest;
use App\Http\Requests\Api\Wiki\Audio\AudioForceDeleteRequest;
use App\Http\Requests\Api\Wiki\Audio\AudioIndexRequest;
use App\Http\Requests\Api\Wiki\Audio\AudioRestoreRequest;
use App\Http\Requests\Api\Wiki\Audio\AudioShowRequest;
use App\Http\Requests\Api\Wiki\Audio\AudioStoreRequest;
use App\Http\Requests\Api\Wiki\Audio\AudioUpdateRequest;
use App\Models\Wiki\Audio;
use Illuminate\Http\JsonResponse;
use Spatie\RouteDiscovery\Attributes\Route;

/**
 * Class AudioController.
 */
class AudioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  AudioIndexRequest  $request
     * @return JsonResponse
     */
    #[Route(fullUri: 'audio', name: 'audio.index')]
    public function index(AudioIndexRequest $request): JsonResponse
    {
        $query = $request->getQuery();

        return $query->index()->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  AudioStoreRequest  $request
     * @return JsonResponse
     */
    #[Route(fullUri: 'audio', name: 'audio.store', middleware: 'auth:sanctum')]
    public function store(AudioStoreRequest $request): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  AudioShowRequest  $request
     * @param  Audio  $audio
     * @return JsonResponse
     */
    #[Route(fullUri: 'audio/{audio}', name: 'audio.show')]
    public function show(AudioShowRequest $request, Audio $audio): JsonResponse
    {
        $resource = $request->getQuery()->show($audio);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  AudioUpdateRequest  $request
     * @param  Audio  $audio
     * @return JsonResponse
     */
    #[Route(fullUri: 'audio/{audio}', name: 'audio.update', middleware: 'auth:sanctum')]
    public function update(AudioUpdateRequest $request, Audio $audio): JsonResponse
    {
        $resource = $request->getQuery()->update($audio);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  AudioDestroyRequest  $request
     * @param  Audio  $audio
     * @return JsonResponse
     */
    #[Route(fullUri: 'audio/{audio}', name: 'audio.destroy', middleware: 'auth:sanctum')]
    public function destroy(AudioDestroyRequest $request, Audio $audio): JsonResponse
    {
        $resource = $request->getQuery()->destroy($audio);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  AudioRestoreRequest  $request
     * @param  Audio  $audio
     * @return JsonResponse
     */
    #[Route(method: 'patch', fullUri: 'restore/audio/{audio}', name: 'audio.restore', middleware: 'auth:sanctum')]
    public function restore(AudioRestoreRequest $request, Audio $audio): JsonResponse
    {
        $resource = $request->getQuery()->restore($audio);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  AudioForceDeleteRequest  $request
     * @param  Audio  $audio
     * @return JsonResponse
     */
    #[Route(method: 'delete', fullUri: 'forceDelete/audio/{audio}', name: 'audio.forceDelete', middleware: 'auth:sanctum')]
    public function forceDelete(AudioForceDeleteRequest $request, Audio $audio): JsonResponse
    {
        return $request->getQuery()->forceDelete($audio);
    }
}
