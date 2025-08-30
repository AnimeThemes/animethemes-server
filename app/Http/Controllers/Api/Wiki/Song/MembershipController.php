<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Song;

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
use App\Http\Resources\Wiki\Song\Collection\MembershipCollection;
use App\Http\Resources\Wiki\Song\Resource\MembershipResource;
use App\Models\Wiki\Song\Membership;
use Illuminate\Http\JsonResponse;

class MembershipController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Membership::class, 'membership');
    }

    /**
     * @param  IndexAction  $action
     */
    public function index(IndexRequest $request, IndexAction $action): MembershipCollection
    {
        $query = new Query($request->validated());

        $memberships = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Membership::query(), $query, $request->schema());

        return new MembershipCollection($memberships, $query);
    }

    /**
     * @param  StoreAction<Membership>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): MembershipResource
    {
        $membership = $action->store(Membership::query(), $request->validated());

        return new MembershipResource($membership, new Query());
    }

    /**
     * @param  ShowAction  $action
     */
    public function show(ShowRequest $request, Membership $membership, ShowAction $action): MembershipResource
    {
        $query = new Query($request->validated());

        $show = $action->show($membership, $query, $request->schema());

        return new MembershipResource($show, $query);
    }

    /**
     * @param  UpdateAction  $action
     */
    public function update(UpdateRequest $request, Membership $membership, UpdateAction $action): MembershipResource
    {
        $updated = $action->update($membership, $request->validated());

        return new MembershipResource($updated, new Query());
    }

    /**
     * @param  DestroyAction  $action
     */
    public function destroy(Membership $membership, DestroyAction $action): MembershipResource
    {
        $deleted = $action->destroy($membership);

        return new MembershipResource($deleted, new Query());
    }

    /**
     * @param  RestoreAction  $action
     */
    public function restore(Membership $membership, RestoreAction $action): MembershipResource
    {
        $restored = $action->restore($membership);

        return new MembershipResource($restored, new Query());
    }

    /**
     * @param  ForceDeleteAction  $action
     */
    public function forceDelete(Membership $membership, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($membership);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
