<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Wiki\Audio\AudioDestroyRequest;
use App\Http\Requests\Api\Wiki\Audio\AudioForceDeleteRequest;
use App\Http\Requests\Api\Wiki\Audio\AudioIndexRequest;
use App\Http\Requests\Api\Wiki\Audio\AudioRestoreRequest;
use App\Http\Requests\Api\Wiki\Audio\AudioShowRequest;
use App\Http\Requests\Api\Wiki\Audio\AudioStoreRequest;
use App\Http\Requests\Api\Wiki\Audio\AudioUpdateRequest;
use App\Models\Wiki\Audio;
use Illuminate\Http\JsonResponse;

/**
 * Class AudioController.
 */
class AudioController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Audio::class, 'audio');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  AudioIndexRequest  $request
     * @return JsonResponse
     */
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
    public function forceDelete(AudioForceDeleteRequest $request, Audio $audio): JsonResponse
    {
        return $request->getQuery()->forceDelete($audio);
    }
}
