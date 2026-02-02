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
use App\Http\Resources\Wiki\Anime\Resource\SynonymJsonResource;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Http\JsonResponse;

class SynonymController extends BaseController
{
    public function __construct()
    {
        parent::__construct(AnimeSynonym::class, 'animesynonym');
    }

    public function index(IndexRequest $request, IndexAction $action): SynonymCollection
    {
        $query = new Query($request->validated());

        $synonyms = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(AnimeSynonym::query(), $query, $request->schema());

        return new SynonymCollection($synonyms, $query);
    }

    /**
     * @param  StoreAction<AnimeSynonym>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): SynonymJsonResource
    {
        $synonym = $action->store(AnimeSynonym::query(), $request->validated());

        return new SynonymJsonResource($synonym, new Query());
    }

    public function show(ShowRequest $request, AnimeSynonym $animesynonym, ShowAction $action): SynonymJsonResource
    {
        $query = new Query($request->validated());

        $show = $action->show($animesynonym, $query, $request->schema());

        return new SynonymJsonResource($show, $query);
    }

    public function update(UpdateRequest $request, AnimeSynonym $animesynonym, UpdateAction $action): SynonymJsonResource
    {
        $updated = $action->update($animesynonym, $request->validated());

        return new SynonymJsonResource($updated, new Query());
    }

    public function destroy(AnimeSynonym $animesynonym, DestroyAction $action): SynonymJsonResource
    {
        $deleted = $action->destroy($animesynonym);

        return new SynonymJsonResource($deleted, new Query());
    }

    public function restore(AnimeSynonym $animesynonym, RestoreAction $action): SynonymJsonResource
    {
        $restored = $action->restore($animesynonym);

        return new SynonymJsonResource($restored, new Query());
    }

    public function forceDelete(AnimeSynonym $animesynonym, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($animesynonym);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
