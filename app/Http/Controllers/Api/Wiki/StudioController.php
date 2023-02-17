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
use Illuminate\Http\Request;

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
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $studios = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Studio::query(), $query, $request->schema());

        $collection = new StudioCollection($studios, $query);

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
        $studio = $action->store(Studio::query(), $request->validated());

        $resource = new StudioResource($studio, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Studio  $studio
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Studio $studio, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($studio, $query, $request->schema());

        $resource = new StudioResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Studio  $studio
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Studio $studio, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($studio, $request->validated());

        $resource = new StudioResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  Studio  $studio
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, Studio $studio, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($studio);

        $resource = new StudioResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  Studio  $studio
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, Studio $studio, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($studio);

        $resource = new StudioResource($restored, new Query());

        return $resource->toResponse($request);
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
