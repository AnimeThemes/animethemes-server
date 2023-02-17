<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Video;

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
use App\Http\Resources\Wiki\Video\Collection\ScriptCollection;
use App\Http\Resources\Wiki\Video\Resource\ScriptResource;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $resources = $action->index(VideoScript::query(), $query, $request->schema());

        $collection = new ScriptCollection($resources, $query);

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
        $script = $action->store(VideoScript::query(), $request->validated());

        $resource = new ScriptResource($script, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  VideoScript  $videoscript
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, VideoScript $videoscript, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($videoscript, $query, $request->schema());

        $resource = new ScriptResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  VideoScript  $videoscript
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, VideoScript $videoscript, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($videoscript, $request->validated());

        $resource = new ScriptResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  VideoScript  $videoscript
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, VideoScript $videoscript, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($videoscript);

        $resource = new ScriptResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  VideoScript  $videoscript
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, VideoScript $videoscript, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($videoscript);

        $resource = new ScriptResource($restored, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  VideoScript  $videoscript
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(VideoScript $videoscript, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($videoscript);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
