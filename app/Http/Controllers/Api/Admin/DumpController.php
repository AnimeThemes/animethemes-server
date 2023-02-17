<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

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
use App\Http\Resources\Admin\Collection\DumpCollection;
use App\Http\Resources\Admin\Resource\DumpResource;
use App\Models\Admin\Dump;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class DumpController.
 */
class DumpController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Dump::class, 'dump');
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

        $dumps = $action->index(Dump::query(), $query, $request->schema());

        $collection = new DumpCollection($dumps, $query);

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
        $dump = $action->store(Dump::query(), $request->validated());

        $resource = new DumpResource($dump, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Dump  $dump
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Dump $dump, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($dump, $query, $request->schema());

        $resource = new DumpResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Dump  $dump
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Dump $dump, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($dump, $request->validated());

        $resource = new DumpResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  Dump  $dump
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, Dump $dump, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($dump);

        $resource = new DumpResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  Dump  $dump
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, Dump $dump, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($dump);

        $resource = new DumpResource($restored, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  Dump  $dump
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(Dump $dump, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($dump);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
