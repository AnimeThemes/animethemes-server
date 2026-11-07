<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List;

use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\ShowAction;
use App\Enums\Models\List\PlaylistVisibility;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\List\Collection\PlaylistCollection;
use App\Http\Resources\List\Resource\PlaylistJsonResource;
use App\Models\List\Playlist;

class PlaylistController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Playlist::class, 'playlist');
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

    public function show(ShowRequest $request, Playlist $playlist, ShowAction $action): PlaylistJsonResource
    {
        $query = new Query($request->validated());

        $show = $action->show($playlist, $query, $request->schema());

        return new PlaylistJsonResource($show, $query);
    }
}
