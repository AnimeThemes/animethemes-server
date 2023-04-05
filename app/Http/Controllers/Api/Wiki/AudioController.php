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
     * @return AudioCollection
     */
    public function index(IndexRequest $request, IndexAction $action): AudioCollection
    {
        $query = new Query($request->validated());

        $audios = $action->index(Audio::query(), $query, $request->schema());

        return new AudioCollection($audios, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction  $action
     * @return AudioResource
     */
    public function store(StoreRequest $request, StoreAction $action): AudioResource
    {
        $audio = $action->store(Audio::query(), $request->validated());

        return new AudioResource($audio, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Audio  $audio
     * @param  ShowAction  $action
     * @return AudioResource
     */
    public function show(ShowRequest $request, Audio $audio, ShowAction $action): AudioResource
    {
        $query = new Query($request->validated());

        $show = $action->show($audio, $query, $request->schema());

        return new AudioResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Audio  $audio
     * @param  UpdateAction  $action
     * @return AudioResource
     */
    public function update(UpdateRequest $request, Audio $audio, UpdateAction $action): AudioResource
    {
        $updated = $action->update($audio, $request->validated());

        return new AudioResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  Audio  $audio
     * @param  DestroyAction  $action
     * @return AudioResource
     */
    public function destroy(Audio $audio, DestroyAction $action): AudioResource
    {
        $deleted = $action->destroy($audio);

        return new AudioResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  Audio  $audio
     * @param  RestoreAction  $action
     * @return AudioResource
     */
    public function restore(Audio $audio, RestoreAction $action): AudioResource
    {
        $restored = $action->restore($audio);

        return new AudioResource($restored, new Query());
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
