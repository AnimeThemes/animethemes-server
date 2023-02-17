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
use Illuminate\Http\Request;

/**
 * Class SongController.
 */
class SongController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Song::class, 'song');
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

        $songs = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Song::query(), $query, $request->schema());

        $collection = new SongCollection($songs, $query);

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
        $song = $action->store(Song::query(), $request->validated());

        $resource = new SongResource($song, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Song  $song
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Song $song, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($song, $query, $request->schema());

        $resource = new SongResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Song  $song
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Song $song, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($song, $request->validated());

        $resource = new SongResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  Song  $song
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, Song $song, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($song);

        $resource = new SongResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  Song  $song
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, Song $song, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($song);

        $resource = new SongResource($restored, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  Song  $song
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(Song $song, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($song);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
