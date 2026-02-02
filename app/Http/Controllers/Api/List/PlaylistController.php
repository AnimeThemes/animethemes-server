<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\IndexAction;
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
use App\Http\Resources\List\Resource\PlaylistJsonResource;
use App\Models\List\Playlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class PlaylistController extends BaseController
{
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
     * @param  StoreAction<Playlist>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): PlaylistJsonResource
    {
        $validated = array_merge(
            $request->validated(),
            [Playlist::ATTRIBUTE_USER => Auth::id()]
        );

        $playlist = $action->store(Playlist::query(), $validated);

        return new PlaylistJsonResource($playlist, new Query());
    }

    public function show(ShowRequest $request, Playlist $playlist, ShowAction $action): PlaylistJsonResource
    {
        $query = new Query($request->validated());

        $show = $action->show($playlist, $query, $request->schema());

        return new PlaylistJsonResource($show, $query);
    }

    public function update(UpdateRequest $request, Playlist $playlist, UpdateAction $action): PlaylistJsonResource
    {
        $updated = $action->update($playlist, $request->validated());

        return new PlaylistJsonResource($updated, new Query());
    }

    public function destroy(Playlist $playlist, DestroyAction $action): JsonResponse
    {
        $message = $action->forceDelete($playlist);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
