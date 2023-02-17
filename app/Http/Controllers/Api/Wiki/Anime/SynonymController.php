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
use Illuminate\Http\Request;

/**
 * Class SynonymController.
 */
class SynonymController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeSynonym::class, 'animesynonym');
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

        $videos = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(AnimeSynonym::query(), $query, $request->schema());

        $collection = new SynonymCollection($videos, $query);

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
        $synonym = $action->store(AnimeSynonym::query(), $request->validated());

        $resource = new SynonymResource($synonym, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  AnimeSynonym  $animesynonym
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, AnimeSynonym $animesynonym, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($animesynonym, $query, $request->schema());

        $resource = new SynonymResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  AnimeSynonym  $animesynonym
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, AnimeSynonym $animesynonym, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($animesynonym, $request->validated());

        $resource = new SynonymResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  AnimeSynonym  $animesynonym
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, AnimeSynonym $animesynonym, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($animesynonym);

        $resource = new SynonymResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  AnimeSynonym  $animesynonym
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, AnimeSynonym $animesynonym, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($animesynonym);

        $resource = new SynonymResource($restored, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  AnimeSynonym  $animesynonym
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(AnimeSynonym $animesynonym, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($animesynonym);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
