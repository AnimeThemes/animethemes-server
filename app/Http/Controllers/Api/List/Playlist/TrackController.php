<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List\Playlist;

use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\List\Playlist\Track\DestroyTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\StoreTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\UpdateTrackAction;
use App\Actions\Http\Api\ShowAction;
use App\Features\AllowPlaylistManagement;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Middleware\Models\List\PlaylistExceedsTrackLimit;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\List\Playlist\Collection\TrackCollection;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class TrackController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(PlaylistTrack::class, 'track,playlist');

        $isPlaylistManagementAllowed = Str::of(EnsureFeaturesAreActive::class)
            ->append(':')
            ->append(AllowPlaylistManagement::class)
            ->__toString();

        $this->middleware($isPlaylistManagementAllowed)->except(['index', 'show']);
        $this->middleware(PlaylistExceedsTrackLimit::class)->only(['store', 'restore']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  Playlist  $playlist
     * @param  IndexAction  $action
     * @return TrackCollection
     */
    public function index(IndexRequest $request, Playlist $playlist, IndexAction $action): TrackCollection
    {
        $query = new Query($request->validated());

        $builder = PlaylistTrack::query()->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist->getKey());

        $resources = $action->index($builder, $query, $request->schema());

        return new TrackCollection($resources, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  Playlist  $playlist
     * @param  StoreTrackAction  $action
     * @return TrackResource
     *
     * @throws Exception
     */
    public function store(StoreRequest $request, Playlist $playlist, StoreTrackAction $action): TrackResource
    {
        $track = $action->store($playlist, PlaylistTrack::query(), $request->validated());

        return new TrackResource($track, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  ShowAction  $action
     * @return TrackResource
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function show(ShowRequest $request, Playlist $playlist, PlaylistTrack $track, ShowAction $action): TrackResource
    {
        $query = new Query($request->validated());

        $show = $action->show($track, $query, $request->schema());

        return new TrackResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  UpdateTrackAction  $action
     * @return TrackResource
     *
     * @throws Exception
     */
    public function update(UpdateRequest $request, Playlist $playlist, PlaylistTrack $track, UpdateTrackAction $action): TrackResource
    {
        $updated = $action->update($playlist, $track, $request->validated());

        return new TrackResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  DestroyTrackAction  $action
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Playlist $playlist, PlaylistTrack $track, DestroyTrackAction $action): JsonResponse
    {
        $message = $action->destroy($playlist, $track);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
