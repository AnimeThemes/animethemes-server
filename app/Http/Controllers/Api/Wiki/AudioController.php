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

class AudioController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Audio::class, 'audio');
    }

    public function index(IndexRequest $request, IndexAction $action): AudioCollection
    {
        $query = new Query($request->validated());

        $audios = $action->index(Audio::query(), $query, $request->schema());

        return new AudioCollection($audios, $query);
    }

    /**
     * @param  StoreAction<Audio>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): AudioResource
    {
        $audio = $action->store(Audio::query(), $request->validated());

        return new AudioResource($audio, new Query());
    }

    public function show(ShowRequest $request, Audio $audio, ShowAction $action): AudioResource
    {
        $query = new Query($request->validated());

        $show = $action->show($audio, $query, $request->schema());

        return new AudioResource($show, $query);
    }

    public function update(UpdateRequest $request, Audio $audio, UpdateAction $action): AudioResource
    {
        $updated = $action->update($audio, $request->validated());

        return new AudioResource($updated, new Query());
    }

    public function destroy(Audio $audio, DestroyAction $action): AudioResource
    {
        $deleted = $action->destroy($audio);

        return new AudioResource($deleted, new Query());
    }

    public function restore(Audio $audio, RestoreAction $action): AudioResource
    {
        $restored = $action->restore($audio);

        return new AudioResource($restored, new Query());
    }

    public function forceDelete(Audio $audio, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($audio);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
