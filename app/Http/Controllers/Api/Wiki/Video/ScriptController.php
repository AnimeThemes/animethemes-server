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

class ScriptController extends BaseController
{
    public function __construct()
    {
        parent::__construct(VideoScript::class, 'videoscript');
    }

    /**
     * @param  IndexAction  $action
     */
    public function index(IndexRequest $request, IndexAction $action): ScriptCollection
    {
        $query = new Query($request->validated());

        $resources = $action->index(VideoScript::query(), $query, $request->schema());

        return new ScriptCollection($resources, $query);
    }

    /**
     * @param  StoreAction<VideoScript>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): ScriptResource
    {
        $script = $action->store(VideoScript::query(), $request->validated());

        return new ScriptResource($script, new Query());
    }

    /**
     * @param  ShowAction  $action
     */
    public function show(ShowRequest $request, VideoScript $videoscript, ShowAction $action): ScriptResource
    {
        $query = new Query($request->validated());

        $show = $action->show($videoscript, $query, $request->schema());

        return new ScriptResource($show, $query);
    }

    /**
     * @param  UpdateAction  $action
     */
    public function update(UpdateRequest $request, VideoScript $videoscript, UpdateAction $action): ScriptResource
    {
        $updated = $action->update($videoscript, $request->validated());

        return new ScriptResource($updated, new Query());
    }

    /**
     * @param  DestroyAction  $action
     */
    public function destroy(VideoScript $videoscript, DestroyAction $action): ScriptResource
    {
        $deleted = $action->destroy($videoscript);

        return new ScriptResource($deleted, new Query());
    }

    /**
     * @param  RestoreAction  $action
     */
    public function restore(VideoScript $videoscript, RestoreAction $action): ScriptResource
    {
        $restored = $action->restore($videoscript);

        return new ScriptResource($restored, new Query());
    }

    /**
     * @param  ForceDeleteAction  $action
     */
    public function forceDelete(VideoScript $videoscript, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($videoscript);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
