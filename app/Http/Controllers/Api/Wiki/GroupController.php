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
use App\Http\Resources\Wiki\Collection\GroupCollection;
use App\Http\Resources\Wiki\Resource\GroupJsonResource;
use App\Models\Wiki\Group;
use Illuminate\Http\JsonResponse;

class GroupController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Group::class, 'group');
    }

    public function index(IndexRequest $request, IndexAction $action): GroupCollection
    {
        $query = new Query($request->validated());

        $groups = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Group::query(), $query, $request->schema());

        return new GroupCollection($groups, $query);
    }

    /**
     * @param  StoreAction<Group>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): GroupJsonResource
    {
        $group = $action->store(Group::query(), $request->validated());

        return new GroupJsonResource($group, new Query());
    }

    public function show(ShowRequest $request, Group $group, ShowAction $action): GroupJsonResource
    {
        $query = new Query($request->validated());

        $show = $action->show($group, $query, $request->schema());

        return new GroupJsonResource($show, $query);
    }

    public function update(UpdateRequest $request, Group $group, UpdateAction $action): GroupJsonResource
    {
        $updated = $action->update($group, $request->validated());

        return new GroupJsonResource($updated, new Query());
    }

    public function destroy(Group $group, DestroyAction $action): GroupJsonResource
    {
        $deleted = $action->destroy($group);

        return new GroupJsonResource($deleted, new Query());
    }

    public function restore(Group $group, RestoreAction $action): GroupJsonResource
    {
        $restored = $action->restore($group);

        return new GroupJsonResource($restored, new Query());
    }

    public function forceDelete(Group $group, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($group);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
