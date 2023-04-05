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

/**
 * Class StudioController.
 */
class StudioController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Studio::class, 'studio');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return StudioCollection
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
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction  $action
     * @return StudioResource
     */
    public function store(StoreRequest $request, StoreAction $action): StudioResource
    {
        $studio = $action->store(Studio::query(), $request->validated());

        return new StudioResource($studio, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Studio  $studio
     * @param  ShowAction  $action
     * @return StudioResource
     */
    public function show(ShowRequest $request, Studio $studio, ShowAction $action): StudioResource
    {
        $query = new Query($request->validated());

        $show = $action->show($studio, $query, $request->schema());

        return new StudioResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Studio  $studio
     * @param  UpdateAction  $action
     * @return StudioResource
     */
    public function update(UpdateRequest $request, Studio $studio, UpdateAction $action): StudioResource
    {
        $updated = $action->update($studio, $request->validated());

        return new StudioResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  Studio  $studio
     * @param  DestroyAction  $action
     * @return StudioResource
     */
    public function destroy(Studio $studio, DestroyAction $action): StudioResource
    {
        $deleted = $action->destroy($studio);

        return new StudioResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  Studio  $studio
     * @param  RestoreAction  $action
     * @return StudioResource
     */
    public function restore(Studio $studio, RestoreAction $action): StudioResource
    {
        $restored = $action->restore($studio);

        return new StudioResource($restored, new Query());
    }

    /**
     * Hard-delete the specified resource.
     *
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
