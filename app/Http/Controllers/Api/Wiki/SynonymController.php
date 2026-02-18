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
use App\Http\Resources\Wiki\Collection\SynonymCollection;
use App\Http\Resources\Wiki\Resource\SynonymJsonResource;
use App\Models\Wiki\Anime\synonym;
use App\Models\Wiki\Synonym;
use Illuminate\Http\JsonResponse;

class SynonymController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Synonym::class, 'synonym');
    }

    public function index(IndexRequest $request, IndexAction $action): SynonymCollection
    {
        $query = new Query($request->validated());

        $synonyms = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Synonym::query(), $query, $request->schema());

        return new SynonymCollection($synonyms, $query);
    }

    /**
     * @param  StoreAction<Synonym>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): SynonymJsonResource
    {
        $synonym = $action->store(Synonym::query(), $request->validated());

        return new SynonymJsonResource($synonym, new Query());
    }

    public function show(ShowRequest $request, Synonym $synonym, ShowAction $action): SynonymJsonResource
    {
        $query = new Query($request->validated());

        $show = $action->show($synonym, $query, $request->schema());

        return new SynonymJsonResource($show, $query);
    }

    public function update(UpdateRequest $request, Synonym $synonym, UpdateAction $action): SynonymJsonResource
    {
        $updated = $action->update($synonym, $request->validated());

        return new SynonymJsonResource($updated, new Query());
    }

    public function destroy(Synonym $synonym, DestroyAction $action): SynonymJsonResource
    {
        $deleted = $action->destroy($synonym);

        return new SynonymJsonResource($deleted, new Query());
    }

    public function restore(Synonym $synonym, RestoreAction $action): SynonymJsonResource
    {
        $restored = $action->restore($synonym);

        return new SynonymJsonResource($restored, new Query());
    }

    public function forceDelete(Synonym $synonym, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($synonym);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
