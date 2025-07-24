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

class AnimeController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Anime::class, 'anime');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexAction  $action
     */
    public function index(IndexRequest $request, IndexAction $action): AnimeCollection
    {
        $query = new Query($request->validated());

        $anime = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Anime::query(), $query, $request->schema());

        return new AnimeCollection($anime, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreAction<Anime>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): AnimeResource
    {
        $anime = $action->store(Anime::query(), $request->validated());

        return new AnimeResource($anime, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowAction  $action
     */
    public function show(ShowRequest $request, Anime $anime, ShowAction $action): AnimeResource
    {
        $query = new Query($request->validated());

        $show = $action->show($anime, $query, $request->schema());

        return new AnimeResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateAction  $action
     */
    public function update(UpdateRequest $request, Anime $anime, UpdateAction $action): AnimeResource
    {
        $updated = $action->update($anime, $request->validated());

        return new AnimeResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  DestroyAction  $action
     */
    public function destroy(Anime $anime, DestroyAction $action): AnimeResource
    {
        $deleted = $action->destroy($anime);

        return new AnimeResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  RestoreAction  $action
     */
    public function restore(Anime $anime, RestoreAction $action): AnimeResource
    {
        $restored = $action->restore($anime);

        return new AnimeResource($restored, new Query());
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  ForceDeleteAction  $action
     */
    public function forceDelete(Anime $anime, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($anime);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
