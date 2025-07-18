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

/**
 * Class MembershipController.
 */
class MembershipController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Membership::class, 'membership');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return MembershipCollection
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
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction<Membership>  $action
     * @return MembershipResource
     */
    public function store(StoreRequest $request, StoreAction $action): MembershipResource
    {
        $membership = $action->store(Membership::query(), $request->validated());

        return new MembershipResource($membership, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Membership  $membership
     * @param  ShowAction  $action
     * @return MembershipResource
     */
    public function show(ShowRequest $request, Membership $membership, ShowAction $action): MembershipResource
    {
        $query = new Query($request->validated());

        $show = $action->show($membership, $query, $request->schema());

        return new MembershipResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Membership  $membership
     * @param  UpdateAction  $action
     * @return MembershipResource
     */
    public function update(UpdateRequest $request, Membership $membership, UpdateAction $action): MembershipResource
    {
        $updated = $action->update($membership, $request->validated());

        return new MembershipResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  Membership  $membership
     * @param  DestroyAction  $action
     * @return MembershipResource
     */
    public function destroy(Membership $membership, DestroyAction $action): MembershipResource
    {
        $deleted = $action->destroy($membership);

        return new MembershipResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  Membership  $membership
     * @param  RestoreAction  $action
     * @return MembershipResource
     */
    public function restore(Membership $membership, RestoreAction $action): MembershipResource
    {
        $restored = $action->restore($membership);

        return new MembershipResource($restored, new Query());
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  Membership  $membership
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(Membership $membership, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($membership);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
