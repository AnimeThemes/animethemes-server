<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Video;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Video\Script\ScriptDestroyRequest;
use App\Http\Requests\Api\Wiki\Video\Script\ScriptForceDeleteRequest;
use App\Http\Requests\Api\Wiki\Video\Script\ScriptIndexRequest;
use App\Http\Requests\Api\Wiki\Video\Script\ScriptRestoreRequest;
use App\Http\Requests\Api\Wiki\Video\Script\ScriptShowRequest;
use App\Http\Requests\Api\Wiki\Video\Script\ScriptStoreRequest;
use App\Http\Requests\Api\Wiki\Video\Script\ScriptUpdateRequest;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Http\JsonResponse;
use Spatie\RouteDiscovery\Attributes\Route;

/**
 * Class ScriptController.
 */
class ScriptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  ScriptIndexRequest  $request
     * @return JsonResponse
     */
    #[Route(fullUri: 'videoscript', name: 'videoscript.index')]
    public function index(ScriptIndexRequest $request): JsonResponse
    {
        $query = $request->getQuery();

        return $query->index()->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  ScriptStoreRequest  $request
     * @return JsonResponse
     */
    #[Route(fullUri: 'videoscript', name: 'videoscript.store', middleware: 'auth:sanctum')]
    public function store(ScriptStoreRequest $request): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ScriptShowRequest  $request
     * @param  VideoScript  $script
     * @return JsonResponse
     */
    #[Route(fullUri: 'videoscript/{videoscript}', name: 'videoscript.show')]
    public function show(ScriptShowRequest $request, VideoScript $script): JsonResponse
    {
        $resource = $request->getQuery()->show($script);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  ScriptUpdateRequest  $request
     * @param  VideoScript  $script
     * @return JsonResponse
     */
    #[Route(fullUri: 'videoscript/{videoscript}', name: 'videoscript.update', middleware: 'auth:sanctum')]
    public function update(ScriptUpdateRequest $request, VideoScript $script): JsonResponse
    {
        $resource = $request->getQuery()->update($script);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  ScriptDestroyRequest  $request
     * @param  VideoScript  $script
     * @return JsonResponse
     */
    #[Route(fullUri: 'videoscript/{videoscript}', name: 'videoscript.destroy', middleware: 'auth:sanctum')]
    public function destroy(ScriptDestroyRequest $request, VideoScript $script): JsonResponse
    {
        $resource = $request->getQuery()->destroy($script);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  ScriptRestoreRequest  $request
     * @param  VideoScript  $script
     * @return JsonResponse
     */
    #[Route(method: 'patch', fullUri: 'restore/videoscript/{videoscript}', name: 'videoscript.restore', middleware: 'auth:sanctum')]
    public function restore(ScriptRestoreRequest $request, VideoScript $script): JsonResponse
    {
        $resource = $request->getQuery()->restore($script);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  ScriptForceDeleteRequest  $request
     * @param  VideoScript  $script
     * @return JsonResponse
     */
    #[Route(method: 'delete', fullUri: 'forceDelete/videoscript/{videoscript}', name: 'videoscript.forceDelete', middleware: 'auth:sanctum')]
    public function forceDelete(ScriptForceDeleteRequest $request, VideoScript $script): JsonResponse
    {
        return $request->getQuery()->forceDelete($script);
    }
}
