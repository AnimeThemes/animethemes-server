<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\ForceDeleteAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\RestoreAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\StoreAction;
use App\Actions\Http\Api\UpdateAction;
use App\Enums\Models\List\PlaylistVisibility;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\List\Collection\PlaylistCollection;
use App\Http\Resources\List\Resource\PlaylistResource;
use App\Models\List\Playlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class PlaylistController.
 */
class PlaylistController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Playlist::class, 'playlist');
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

        $builder = Playlist::query()->where(Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::PUBLIC);

        $playlists = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index($builder, $query, $request->schema());

        $collection = new PlaylistCollection($playlists, $query);

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
        $validated = array_merge(
            $request->validated(),
            [Playlist::ATTRIBUTE_USER => Auth::id()]
        );

        $playlist = $action->store(Playlist::query(), $validated);

        $resource = new PlaylistResource($playlist, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Playlist  $playlist
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Playlist $playlist, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($playlist, $query, $request->schema());

        $resource = new PlaylistResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Playlist  $playlist
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Playlist $playlist, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($playlist, $request->validated());

        $resource = new PlaylistResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  Playlist  $playlist
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, Playlist $playlist, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($playlist);

        $resource = new PlaylistResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  Playlist  $playlist
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, Playlist $playlist, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($playlist);

        $resource = new PlaylistResource($restored, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  Playlist  $playlist
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(Playlist $playlist, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($playlist);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
