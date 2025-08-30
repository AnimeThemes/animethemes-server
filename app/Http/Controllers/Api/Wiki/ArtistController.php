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
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Models\Wiki\Artist;
use Illuminate\Http\JsonResponse;

class ArtistController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Artist::class, 'artist');
    }

    /**
     * @param  IndexAction  $action
     */
    public function index(IndexRequest $request, IndexAction $action): ArtistCollection
    {
        $query = new Query($request->validated());

        $artists = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Artist::query(), $query, $request->schema());

        return new ArtistCollection($artists, $query);
    }

    /**
     * @param  StoreAction<Artist>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): ArtistResource
    {
        $artist = $action->store(Artist::query(), $request->validated());

        return new ArtistResource($artist, new Query());
    }

    /**
     * @param  ShowAction  $action
     */
    public function show(ShowRequest $request, Artist $artist, ShowAction $action): ArtistResource
    {
        $query = new Query($request->validated());

        $show = $action->show($artist, $query, $request->schema());

        return new ArtistResource($show, $query);
    }

    /**
     * @param  UpdateAction  $action
     */
    public function update(UpdateRequest $request, Artist $artist, UpdateAction $action): ArtistResource
    {
        $updated = $action->update($artist, $request->validated());

        return new ArtistResource($updated, new Query());
    }

    /**
     * @param  DestroyAction  $action
     */
    public function destroy(Artist $artist, DestroyAction $action): ArtistResource
    {
        $deleted = $action->destroy($artist);

        return new ArtistResource($deleted, new Query());
    }

    /**
     * @param  RestoreAction  $action
     */
    public function restore(Artist $artist, RestoreAction $action): ArtistResource
    {
        $restored = $action->restore($artist);

        return new ArtistResource($restored, new Query());
    }

    /**
     * @param  ForceDeleteAction  $action
     */
    public function forceDelete(Artist $artist, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($artist);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
