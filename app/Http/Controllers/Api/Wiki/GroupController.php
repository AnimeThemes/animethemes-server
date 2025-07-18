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
use App\Http\Resources\Wiki\Resource\GroupResource;
use App\Models\Wiki\Group;
use Illuminate\Http\JsonResponse;

/**
 * Class GroupController.
 */
class GroupController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Group::class, 'group');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return GroupCollection
     */
    public function index(IndexRequest $request, IndexAction $action): GroupCollection
    {
        $query = new Query($request->validated());

        $groups = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Group::query(), $query, $request->schema());

        return new GroupCollection($groups, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction<Group>  $action
     * @return GroupResource
     */
    public function store(StoreRequest $request, StoreAction $action): GroupResource
    {
        $group = $action->store(Group::query(), $request->validated());

        return new GroupResource($group, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Group  $group
     * @param  ShowAction  $action
     * @return GroupResource
     */
    public function show(ShowRequest $request, Group $group, ShowAction $action): GroupResource
    {
        $query = new Query($request->validated());

        $show = $action->show($group, $query, $request->schema());

        return new GroupResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Group  $group
     * @param  UpdateAction  $action
     * @return GroupResource
     */
    public function update(UpdateRequest $request, Group $group, UpdateAction $action): GroupResource
    {
        $updated = $action->update($group, $request->validated());

        return new GroupResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  Group  $group
     * @param  DestroyAction  $action
     * @return GroupResource
     */
    public function destroy(Group $group, DestroyAction $action): GroupResource
    {
        $deleted = $action->destroy($group);

        return new GroupResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  Group  $group
     * @param  RestoreAction  $action
     * @return GroupResource
     */
    public function restore(Group $group, RestoreAction $action): GroupResource
    {
        $restored = $action->restore($group);

        return new GroupResource($restored, new Query());
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  Group  $group
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(Group $group, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($group);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
