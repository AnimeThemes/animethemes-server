<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List\Playlist;

use App\Actions\Http\Api\IndexAction;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\List\Playlist\ForwardBackwardSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\List\Playlist\ForwardBackwardIndexRequest;
use App\Http\Resources\List\Playlist\Collection\TrackCollection;
use App\Models\List\Playlist;
use App\Models\List\Playlist\BackwardPlaylistTrack;
use App\Models\List\Playlist\PlaylistTrack;

/**
 * Class TrackBackwardController.
 */
class TrackBackwardController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(PlaylistTrack::class, 'track,playlist');
    }

    /**
     * Display the specified resource.
     *
     * @param  ForwardBackwardIndexRequest  $request
     * @param  Playlist  $playlist
     * @param  BackwardPlaylistTrack  $track
     * @param  IndexAction  $action
     * @return TrackCollection
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function index(ForwardBackwardIndexRequest $request, Playlist $playlist, BackwardPlaylistTrack $track, IndexAction $action): TrackCollection
    {
        $query = new Query($request->validated());

        $builder = $track->descendants()->getQuery();

        $resources = $action->index($builder, $query, $request->schema());

        return new TrackCollection($resources, $query);
    }

    /**
     * Get the underlying schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new ForwardBackwardSchema();
    }
}
