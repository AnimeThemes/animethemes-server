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
use App\Features\AllowPlaylistManagement;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Middleware\Models\List\UserExceedsPlaylistLimit;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\List\Collection\PlaylistCollection;
use App\Http\Resources\List\Resource\PlaylistResource;
use App\Models\List\Playlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

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

        $isPlaylistManagementAllowed = Str::of(EnsureFeaturesAreActive::class)
            ->append(':')
            ->append(AllowPlaylistManagement::class)
            ->__toString();

        $this->middleware($isPlaylistManagementAllowed)->except(['index', 'show']);
        $this->middleware(UserExceedsPlaylistLimit::class)->only(['store', 'restore']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return PlaylistCollection
     */
    public function index(IndexRequest $request, IndexAction $action): PlaylistCollection
    {
        $query = new Query($request->validated());

        $builder = Playlist::query()->where(Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::PUBLIC->value);

        $playlists = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index($builder, $query, $request->schema());

        return new PlaylistCollection($playlists, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction  $action
     * @return PlaylistResource
     */
    public function store(StoreRequest $request, StoreAction $action): PlaylistResource
    {
        $validated = array_merge(
            $request->validated(),
            [Playlist::ATTRIBUTE_USER => Auth::id()]
        );

        $playlist = $action->store(Playlist::query(), $validated);

        return new PlaylistResource($playlist, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Playlist  $playlist
     * @param  ShowAction  $action
     * @return PlaylistResource
     */
    public function show(ShowRequest $request, Playlist $playlist, ShowAction $action): PlaylistResource
    {
        $query = new Query($request->validated());

        $show = $action->show($playlist, $query, $request->schema());

        return new PlaylistResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Playlist  $playlist
     * @param  UpdateAction  $action
     * @return PlaylistResource
     */
    public function update(UpdateRequest $request, Playlist $playlist, UpdateAction $action): PlaylistResource
    {
        $updated = $action->update($playlist, $request->validated());

        return new PlaylistResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  Playlist  $playlist
     * @param  DestroyAction  $action
     * @return PlaylistResource
     */
    public function destroy(Playlist $playlist, DestroyAction $action): PlaylistResource
    {
        $deleted = $action->destroy($playlist);

        return new PlaylistResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  Playlist  $playlist
     * @param  RestoreAction  $action
     * @return PlaylistResource
     */
    public function restore(Playlist $playlist, RestoreAction $action): PlaylistResource
    {
        $restored = $action->restore($playlist);

        return new PlaylistResource($restored, new Query());
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
