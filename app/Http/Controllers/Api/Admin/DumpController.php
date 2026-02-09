<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\IndexAction;
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
use App\Http\Resources\Admin\Resource\DumpJsonResource;
use App\Models\Admin\Dump;
use Illuminate\Http\JsonResponse;

class DumpController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Dump::class, 'dump');
    }

    public function index(IndexRequest $request, IndexAction $action): DumpCollection
    {
        $query = new Query($request->validated());

        /** @phpstan-ignore-next-line */
        $dumps = $action->index(Dump::query()->public(), $query, $request->schema());

        return new DumpCollection($dumps, $query);
    }

    /**
     * @param  StoreAction<Dump>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): DumpJsonResource
    {
        $dump = $action->store(Dump::query(), $request->validated());

        return new DumpJsonResource($dump, new Query());
    }

    public function show(ShowRequest $request, Dump $dump, ShowAction $action): DumpJsonResource
    {
        $query = new Query($request->validated());

        $show = $action->show($dump, $query, $request->schema());

        return new DumpJsonResource($show, $query);
    }

    public function update(UpdateRequest $request, Dump $dump, UpdateAction $action): DumpJsonResource
    {
        $updated = $action->update($dump, $request->validated());

        return new DumpJsonResource($updated, new Query());
    }

    public function destroy(Dump $dump, DestroyAction $action): JsonResponse
    {
        $message = $action->forceDelete($dump);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
