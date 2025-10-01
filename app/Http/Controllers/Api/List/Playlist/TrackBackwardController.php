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

class TrackBackwardController extends BaseController
{
    public function __construct()
    {
        parent::__construct(PlaylistTrack::class, 'track,playlist');
    }

    /**
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
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function schema(): ForwardBackwardSchema
    {
        return new ForwardBackwardSchema();
    }
}
