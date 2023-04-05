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
     * @return AnimeCollection
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
     * @param  StoreRequest  $request
     * @param  StoreAction  $action
     * @return AnimeResource
     */
    public function store(StoreRequest $request, StoreAction $action): AnimeResource
    {
        $anime = $action->store(Anime::query(), $request->validated());

        return new AnimeResource($anime, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Anime  $anime
     * @param  ShowAction  $action
     * @return AnimeResource
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
     * @param  UpdateRequest  $request
     * @param  Anime  $anime
     * @param  UpdateAction  $action
     * @return AnimeResource
     */
    public function update(UpdateRequest $request, Anime $anime, UpdateAction $action): AnimeResource
    {
        $updated = $action->update($anime, $request->validated());

        return new AnimeResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  Anime  $anime
     * @param  DestroyAction  $action
     * @return AnimeResource
     */
    public function destroy(Anime $anime, DestroyAction $action): AnimeResource
    {
        $deleted = $action->destroy($anime);

        return new AnimeResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  Anime  $anime
     * @param  RestoreAction  $action
     * @return AnimeResource
     */
    public function restore(Anime $anime, RestoreAction $action): AnimeResource
    {
        $restored = $action->restore($anime);

        return new AnimeResource($restored, new Query());
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
