<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List\Playlist;

use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\ShowAction;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\List\Playlist\Collection\TrackCollection;
use App\Http\Resources\List\Playlist\Resource\TrackJsonResource;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;

class TrackController extends BaseController
{
    public function __construct()
    {
        parent::__construct(PlaylistTrack::class, 'track,playlist');
    }

    public function index(IndexRequest $request, Playlist $playlist, IndexAction $action): TrackCollection
    {
        $query = new Query($request->validated());

        $builder = PlaylistTrack::query()->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist->getKey());

        $resources = $action->index($builder, $query, $request->schema());

        return new TrackCollection($resources, $query);
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     */
    public function show(ShowRequest $request, Playlist $playlist, PlaylistTrack $track, ShowAction $action): TrackJsonResource
    {
        $query = new Query($request->validated());

        $show = $action->show($track, $query, $request->schema());

        return new TrackJsonResource($show, $query);
    }
}
