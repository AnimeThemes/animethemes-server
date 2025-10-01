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
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\Wiki\Song;
use Illuminate\Http\JsonResponse;

class SongController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Song::class, 'song');
    }

    public function index(IndexRequest $request, IndexAction $action): SongCollection
    {
        $query = new Query($request->validated());

        $songs = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Song::query(), $query, $request->schema());

        return new SongCollection($songs, $query);
    }

    /**
     * @param  StoreAction<Song>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): SongResource
    {
        $song = $action->store(Song::query(), $request->validated());

        return new SongResource($song, new Query());
    }

    public function show(ShowRequest $request, Song $song, ShowAction $action): SongResource
    {
        $query = new Query($request->validated());

        $show = $action->show($song, $query, $request->schema());

        return new SongResource($show, $query);
    }

    public function update(UpdateRequest $request, Song $song, UpdateAction $action): SongResource
    {
        $updated = $action->update($song, $request->validated());

        return new SongResource($updated, new Query());
    }

    public function destroy(Song $song, DestroyAction $action): SongResource
    {
        $deleted = $action->destroy($song);

        return new SongResource($deleted, new Query());
    }

    public function restore(Song $song, RestoreAction $action): SongResource
    {
        $restored = $action->restore($song);

        return new SongResource($restored, new Query());
    }

    public function forceDelete(Song $song, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($song);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
