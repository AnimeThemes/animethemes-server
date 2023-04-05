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
     * @return DumpCollection
     */
    public function index(IndexRequest $request, IndexAction $action): DumpCollection
    {
        $query = new Query($request->validated());

        $dumps = $action->index(Dump::query(), $query, $request->schema());

        return new DumpCollection($dumps, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction  $action
     * @return DumpResource
     */
    public function store(StoreRequest $request, StoreAction $action): DumpResource
    {
        $dump = $action->store(Dump::query(), $request->validated());

        return new DumpResource($dump, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Dump  $dump
     * @param  ShowAction  $action
     * @return DumpResource
     */
    public function show(ShowRequest $request, Dump $dump, ShowAction $action): DumpResource
    {
        $query = new Query($request->validated());

        $show = $action->show($dump, $query, $request->schema());

        return new DumpResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Dump  $dump
     * @param  UpdateAction  $action
     * @return DumpResource
     */
    public function update(UpdateRequest $request, Dump $dump, UpdateAction $action): DumpResource
    {
        $updated = $action->update($dump, $request->validated());

        return new DumpResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  Dump  $dump
     * @param  DestroyAction  $action
     * @return DumpResource
     */
    public function destroy(Dump $dump, DestroyAction $action): DumpResource
    {
        $deleted = $action->destroy($dump);

        return new DumpResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  Dump  $dump
     * @param  RestoreAction  $action
     * @return DumpResource
     */
    public function restore(Dump $dump, RestoreAction $action): DumpResource
    {
        $restored = $action->restore($dump);

        return new DumpResource($restored, new Query());
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
