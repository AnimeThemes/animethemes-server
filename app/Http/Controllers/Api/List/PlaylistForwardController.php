<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List;

use App\Actions\Http\Api\IndexAction;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\List\Playlist\ForwardBackwardSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\List\Playlist\ForwardBackwardIndexRequest;
use App\Http\Resources\List\Playlist\Collection\TrackCollection;
use App\Models\List\Playlist;
use App\Models\List\Playlist\ForwardPlaylistTrack;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class PlaylistForwardController.
 */
class PlaylistForwardController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(PlaylistTrack::class, 'track,playlist');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  ForwardBackwardIndexRequest  $request
     * @param  Playlist  $playlist
     * @param  IndexAction  $action
     * @return TrackCollection
     */
    public function index(ForwardBackwardIndexRequest $request, Playlist $playlist, IndexAction $action): TrackCollection
    {
        $query = new Query($request->validated());

        $constraint = function (Builder $query) use ($playlist) {
            $query->where(PlaylistTrack::ATTRIBUTE_ID, $playlist->first_id);
        };

        $builder = ForwardPlaylistTrack::query()->treeOf($constraint);

        $resources = $action->index($builder, $query, $request->schema());

        return new TrackCollection($resources, $query);
    }

    /**
     * Get the underlying schema.
     *
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function schema(): Schema
    {
        return new ForwardBackwardSchema();
    }
}
