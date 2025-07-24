<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Pivot\Wiki;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\StoreAction;
use App\Actions\Http\Api\UpdateAction;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\Pivot\PivotController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\Pivot\Wiki\Collection\SongResourceCollection;
use App\Http\Resources\Pivot\Wiki\Resource\SongResourceResource;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\SongResource;
use Illuminate\Http\JsonResponse;

class SongResourceController extends PivotController
{
    public function __construct()
    {
        parent::__construct(Song::class, 'song', ExternalResource::class, 'resource');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return SongResourceCollection
     */
    public function index(IndexRequest $request, IndexAction $action): SongResourceCollection
    {
        $query = new Query($request->validated());

        $resources = $action->index(SongResource::query(), $query, $request->schema());

        return new SongResourceCollection($resources, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  Song  $song
     * @param  ExternalResource  $resource
     * @param  StoreAction<SongResource>  $action
     * @return SongResourceResource
     */
    public function store(StoreRequest $request, Song $song, ExternalResource $resource, StoreAction $action): SongResourceResource
    {
        $validated = array_merge(
            $request->validated(),
            [
                SongResource::ATTRIBUTE_SONG => $song->getKey(),
                SongResource::ATTRIBUTE_RESOURCE => $resource->getKey(),
            ]
        );

        $songResource = $action->store(SongResource::query(), $validated);

        return new SongResourceResource($songResource, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Song  $song
     * @param  ExternalResource  $resource
     * @param  ShowAction  $action
     * @return SongResourceResource
     */
    public function show(ShowRequest $request, Song $song, ExternalResource $resource, ShowAction $action): SongResourceResource
    {
        $songResource = SongResource::query()
            ->where(SongResource::ATTRIBUTE_SONG, $song->getKey())
            ->where(SongResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($songResource, $query, $request->schema());

        return new SongResourceResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Song  $song
     * @param  ExternalResource  $resource
     * @param  UpdateAction  $action
     * @return SongResourceResource
     */
    public function update(UpdateRequest $request, Song $song, ExternalResource $resource, UpdateAction $action): SongResourceResource
    {
        $songResource = SongResource::query()
            ->where(SongResource::ATTRIBUTE_SONG, $song->getKey())
            ->where(SongResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $updated = $action->update($songResource, $request->validated());

        return new SongResourceResource($updated, $query);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Song  $song
     * @param  ExternalResource  $resource
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Song $song, ExternalResource $resource, DestroyAction $action): JsonResponse
    {
        $songResource = SongResource::query()
            ->where(SongResource::ATTRIBUTE_SONG, $song->getKey())
            ->where(SongResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->firstOrFail();

        $action->destroy($songResource);

        return new JsonResponse([
            'message' => "Resource '{$resource->getName()}' has been detached from Song '{$song->getName()}'.",
        ]);
    }
}
