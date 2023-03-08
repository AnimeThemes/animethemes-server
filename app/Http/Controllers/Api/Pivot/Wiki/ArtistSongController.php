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
use App\Http\Resources\Pivot\Wiki\Collection\ArtistSongCollection;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistSongResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Http\JsonResponse;

/**
 * Class ArtistSongController.
 */
class ArtistSongController extends PivotController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Artist::class, 'artist', Song::class, 'song');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $resources = $action->index(ArtistSong::query(), $query, $request->schema());

        $collection = new ArtistSongCollection($resources, $query);

        return $collection->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction  $action
     * @return JsonResponse
     */
    public function store(StoreRequest $request, StoreAction $action): JsonResponse
    {
        $artistSong = $action->store(ArtistSong::query(), $request->validated());

        $resource = new ArtistSongResource($artistSong, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Artist  $artist
     * @param  Song  $song
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Artist $artist, Song $song, ShowAction $action): JsonResponse
    {
        $artistSong = ArtistSong::query()
            ->where(ArtistSong::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistSong::ATTRIBUTE_SONG, $song->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($artistSong, $query, $request->schema());

        $resource = new ArtistSongResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Artist  $artist
     * @param  Song  $song
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Artist $artist, Song $song, UpdateAction $action): JsonResponse
    {
        $artistSong = ArtistSong::query()
            ->where(ArtistSong::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistSong::ATTRIBUTE_SONG, $song->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $updated = $action->update($artistSong, $request->validated());

        $apiResource = new ArtistSongResource($updated, $query);

        return $apiResource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Artist  $artist
     * @param  Song  $song
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Artist $artist, Song $song, DestroyAction $action): JsonResponse
    {
        $artistSong = ArtistSong::query()
            ->where(ArtistSong::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistSong::ATTRIBUTE_SONG, $song->getKey())
            ->firstOrFail();

        $action->destroy($artistSong);

        return new JsonResponse([
            'message' => "Song '{$song->getName()}' has been detached from Artist '{$artist->getName()}'.",
        ]);
    }
}
