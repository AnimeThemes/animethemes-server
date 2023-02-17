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
use Illuminate\Http\Request;

/**
 * Class ArtistController.
 */
class ArtistController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Artist::class, 'artist');
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

        $artists = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Artist::query(), $query, $request->schema());

        $collection = new ArtistCollection($artists, $query);

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
        $artist = $action->store(Artist::query(), $request->validated());

        $resource = new ArtistResource($artist, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Artist  $artist
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Artist $artist, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($artist, $query, $request->schema());

        $resource = new ArtistResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Artist  $artist
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Artist $artist, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($artist, $request->validated());

        $resource = new ArtistResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  Artist  $artist
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, Artist $artist, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($artist);

        $resource = new ArtistResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  Artist  $artist
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, Artist $artist, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($artist);

        $resource = new ArtistResource($restored, new Query());

        return $resource->toResponse($request);
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
