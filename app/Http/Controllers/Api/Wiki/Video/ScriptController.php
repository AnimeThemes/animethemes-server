<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Video;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Wiki\Video\Script\ScriptDestroyRequest;
use App\Http\Requests\Api\Wiki\Video\Script\ScriptForceDeleteRequest;
use App\Http\Requests\Api\Wiki\Video\Script\ScriptIndexRequest;
use App\Http\Requests\Api\Wiki\Video\Script\ScriptRestoreRequest;
use App\Http\Requests\Api\Wiki\Video\Script\ScriptShowRequest;
use App\Http\Requests\Api\Wiki\Video\Script\ScriptStoreRequest;
use App\Http\Requests\Api\Wiki\Video\Script\ScriptUpdateRequest;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Http\JsonResponse;

/**
 * Class ScriptController.
 */
class ScriptController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(VideoScript::class, 'videoscript');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  ScriptIndexRequest  $request
     * @return JsonResponse
     */
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
    public function store(ScriptStoreRequest $request): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ScriptShowRequest  $request
     * @param  VideoScript  $videoscript
     * @return JsonResponse
     */
    public function show(ScriptShowRequest $request, VideoScript $videoscript): JsonResponse
    {
        $resource = $request->getQuery()->show($videoscript);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  ScriptUpdateRequest  $request
     * @param  VideoScript  $videoscript
     * @return JsonResponse
     */
    public function update(ScriptUpdateRequest $request, VideoScript $videoscript): JsonResponse
    {
        $resource = $request->getQuery()->update($videoscript);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  ScriptDestroyRequest  $request
     * @param  VideoScript  $videoscript
     * @return JsonResponse
     */
    public function destroy(ScriptDestroyRequest $request, VideoScript $videoscript): JsonResponse
    {
        $resource = $request->getQuery()->destroy($videoscript);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  ScriptRestoreRequest  $request
     * @param  VideoScript  $videoscript
     * @return JsonResponse
     */
    public function restore(ScriptRestoreRequest $request, VideoScript $videoscript): JsonResponse
    {
        $resource = $request->getQuery()->restore($videoscript);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  ScriptForceDeleteRequest  $request
     * @param  VideoScript  $videoscript
     * @return JsonResponse
     */
    public function forceDelete(ScriptForceDeleteRequest $request, VideoScript $videoscript): JsonResponse
    {
        return $request->getQuery()->forceDelete($videoscript);
    }
}
