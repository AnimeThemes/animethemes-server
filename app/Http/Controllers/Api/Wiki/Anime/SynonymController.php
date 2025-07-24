<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Anime;

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
use App\Http\Resources\Wiki\Anime\Collection\SynonymCollection;
use App\Http\Resources\Wiki\Anime\Resource\SynonymResource;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Http\JsonResponse;

class SynonymController extends BaseController
{
    public function __construct()
    {
        parent::__construct(AnimeSynonym::class, 'animesynonym');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexAction  $action
     */
    public function index(IndexRequest $request, IndexAction $action): SynonymCollection
    {
        $query = new Query($request->validated());

        $synonyms = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(AnimeSynonym::query(), $query, $request->schema());

        return new SynonymCollection($synonyms, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreAction<AnimeSynonym>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): SynonymResource
    {
        $synonym = $action->store(AnimeSynonym::query(), $request->validated());

        return new SynonymResource($synonym, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowAction  $action
     */
    public function show(ShowRequest $request, AnimeSynonym $animesynonym, ShowAction $action): SynonymResource
    {
        $query = new Query($request->validated());

        $show = $action->show($animesynonym, $query, $request->schema());

        return new SynonymResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateAction  $action
     */
    public function update(UpdateRequest $request, AnimeSynonym $animesynonym, UpdateAction $action): SynonymResource
    {
        $updated = $action->update($animesynonym, $request->validated());

        return new SynonymResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  DestroyAction  $action
     */
    public function destroy(AnimeSynonym $animesynonym, DestroyAction $action): SynonymResource
    {
        $deleted = $action->destroy($animesynonym);

        return new SynonymResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  RestoreAction  $action
     */
    public function restore(AnimeSynonym $animesynonym, RestoreAction $action): SynonymResource
    {
        $restored = $action->restore($animesynonym);

        return new SynonymResource($restored, new Query());
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  ForceDeleteAction  $action
     */
    public function forceDelete(AnimeSynonym $animesynonym, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($animesynonym);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
