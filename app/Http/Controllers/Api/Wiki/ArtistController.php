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
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return ArtistCollection
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
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction<Artist>  $action
     * @return ArtistResource
     */
    public function store(StoreRequest $request, StoreAction $action): ArtistResource
    {
        $artist = $action->store(Artist::query(), $request->validated());

        return new ArtistResource($artist, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Artist  $artist
     * @param  ShowAction  $action
     * @return ArtistResource
     */
    public function show(ShowRequest $request, Artist $artist, ShowAction $action): ArtistResource
    {
        $query = new Query($request->validated());

        $show = $action->show($artist, $query, $request->schema());

        return new ArtistResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Artist  $artist
     * @param  UpdateAction  $action
     * @return ArtistResource
     */
    public function update(UpdateRequest $request, Artist $artist, UpdateAction $action): ArtistResource
    {
        $updated = $action->update($artist, $request->validated());

        return new ArtistResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  Artist  $artist
     * @param  DestroyAction  $action
     * @return ArtistResource
     */
    public function destroy(Artist $artist, DestroyAction $action): ArtistResource
    {
        $deleted = $action->destroy($artist);

        return new ArtistResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  Artist  $artist
     * @param  RestoreAction  $action
     * @return ArtistResource
     */
    public function restore(Artist $artist, RestoreAction $action): ArtistResource
    {
        $restored = $action->restore($artist);

        return new ArtistResource($restored, new Query());
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  Artist  $artist
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(Artist $artist, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($artist);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
