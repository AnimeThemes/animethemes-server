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
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class AnimeController.
 */
class AnimeController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Anime::class, 'anime');
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

        $anime = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Anime::query(), $query, $request->schema());

        $collection = new AnimeCollection($anime, $query);

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
        $anime = $action->store(Anime::query(), $request->validated());

        $resource = new AnimeResource($anime, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Anime  $anime
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Anime $anime, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($anime, $query, $request->schema());

        $resource = new AnimeResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Anime  $anime
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Anime $anime, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($anime, $request->validated());

        $resource = new AnimeResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  Anime  $anime
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, Anime $anime, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($anime);

        $resource = new AnimeResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  Anime  $anime
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, Anime $anime, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($anime);

        $resource = new AnimeResource($restored, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  Anime  $anime
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(Anime $anime, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($anime);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
