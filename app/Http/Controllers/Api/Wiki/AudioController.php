<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

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
use App\Http\Resources\Wiki\Collection\AudioCollection;
use App\Http\Resources\Wiki\Resource\AudioResource;
use App\Models\Wiki\Audio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $audios = $action->index(Audio::query(), $query, $request->schema());

        $collection = new AudioCollection($audios, $query);

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
        $audio = $action->store(Audio::query(), $request->validated());

        $resource = new AudioResource($audio, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Audio  $audio
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Audio $audio, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($audio, $query, $request->schema());

        $resource = new AudioResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Audio  $audio
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Audio $audio, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($audio, $request->validated());

        $resource = new AudioResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  Audio  $audio
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, Audio $audio, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($audio);

        $resource = new AudioResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  Audio  $audio
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, Audio $audio, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($audio);

        $resource = new AudioResource($restored, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  Audio  $audio
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(Audio $audio, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($audio);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
