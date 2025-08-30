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
use App\Http\Resources\Wiki\Collection\StudioCollection;
use App\Http\Resources\Wiki\Resource\StudioResource;
use App\Models\Wiki\Studio;
use Illuminate\Http\JsonResponse;

class StudioController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Studio::class, 'studio');
    }

    /**
     * @param  IndexAction  $action
     */
    public function index(IndexRequest $request, IndexAction $action): StudioCollection
    {
        $query = new Query($request->validated());

        $studios = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Studio::query(), $query, $request->schema());

        return new StudioCollection($studios, $query);
    }

    /**
     * @param  StoreAction<Studio>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): StudioResource
    {
        $studio = $action->store(Studio::query(), $request->validated());

        return new StudioResource($studio, new Query());
    }

    /**
     * @param  ShowAction  $action
     */
    public function show(ShowRequest $request, Studio $studio, ShowAction $action): StudioResource
    {
        $query = new Query($request->validated());

        $show = $action->show($studio, $query, $request->schema());

        return new StudioResource($show, $query);
    }

    /**
     * @param  UpdateAction  $action
     */
    public function update(UpdateRequest $request, Studio $studio, UpdateAction $action): StudioResource
    {
        $updated = $action->update($studio, $request->validated());

        return new StudioResource($updated, new Query());
    }

    /**
     * @param  DestroyAction  $action
     */
    public function destroy(Studio $studio, DestroyAction $action): StudioResource
    {
        $deleted = $action->destroy($studio);

        return new StudioResource($deleted, new Query());
    }

    /**
     * @param  RestoreAction  $action
     */
    public function restore(Studio $studio, RestoreAction $action): StudioResource
    {
        $restored = $action->restore($studio);

        return new StudioResource($restored, new Query());
    }

    /**
     * @param  Studio  $studio
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(Studio $studio, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($studio);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
