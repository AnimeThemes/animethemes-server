<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List\External;

use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\List\External\Entry\DestroyEntryAction;
use App\Actions\Http\Api\List\External\Entry\ForceDeleteEntryAction;
use App\Actions\Http\Api\List\External\Entry\RestoreEntryAction;
use App\Actions\Http\Api\List\External\Entry\StoreEntryAction;
use App\Actions\Http\Api\List\External\Entry\UpdateEntryAction;
use App\Actions\Http\Api\ShowAction;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\List\External\Collection\ExternalEntryCollection;
use App\Http\Resources\List\External\Resource\ExternalEntryResource;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use Illuminate\Http\JsonResponse;

/**
 * Class ExternalEntryController.
 */
class ExternalEntryController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(ExternalEntry::class, 'externalentry,externalprofile');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  ExternalProfile  $profile
     * @param  IndexAction  $action
     * @return ExternalEntryCollection
     */
    public function index(IndexRequest $request, ExternalProfile $profile, IndexAction $action): ExternalEntryCollection
    {
        $query = new Query($request->validated());

        $builder = ExternalEntry::query()->where(ExternalEntry::ATTRIBUTE_EXTERNAL_PROFILE, $profile->getKey());

        $resources = $action->index($builder, $query, $request->schema());

        return new ExternalEntryCollection($resources, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  ExternalProfile  $profile
     * @param  StoreEntryAction  $action
     * @return ExternalEntryResource
     */
    public function store(StoreRequest $request, ExternalProfile $profile, StoreEntryAction $action): ExternalEntryResource
    {
        $entry = $action->store($profile, ExternalEntry::query(), $request->validated());

        return new ExternalEntryResource($entry, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  ExternalProfile  $profile
     * @param  ExternalEntry  $entry
     * @param  ShowAction  $action
     * @return ExternalEntryResource
     */
    public function show(ShowRequest $request, ExternalProfile $profile, ExternalEntry $entry, ShowAction $action): ExternalEntryResource
    {
        $query = new Query($request->validated());

        $show = $action->show($entry, $query, $request->schema());

        return new ExternalEntryResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  ExternalProfile  $profile
     * @param  ExternalEntry  $entry
     * @param  UpdateEntryAction  $action
     * @return ExternalEntryResource
     */
    public function update(UpdateRequest $request, ExternalProfile $profile, ExternalEntry $entry, UpdateEntryAction $action): ExternalEntryResource
    {
        $updated = $action->update($entry, $request->validated());

        return new ExternalEntryResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  ExternalProfile  $profile
     * @param  ExternalEntry  $entry
     * @param  DestroyEntryAction  $action
     * @return ExternalEntryResource
     */
    public function destroy(ExternalProfile $profile, ExternalEntry $entry, DestroyEntryAction $action): ExternalEntryResource
    {
        $deleted = $action->destroy($entry);

        return new ExternalEntryResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  ExternalProfile  $profile
     * @param  ExternalEntry  $entry
     * @param  RestoreEntryAction  $action
     * @return ExternalEntryResource
     */
    public function restore(ExternalProfile $profile, ExternalEntry $entry, RestoreEntryAction $action): ExternalEntryResource
    {
        $restored = $action->restore($profile, $entry);

        return new ExternalEntryResource($restored, new Query());
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  ExternalProfile  $profile
     * @param  ExternalEntry  $entry
     * @param  ForceDeleteEntryAction  $action
     * @return JsonResponse
     */
    public function forceDelete(ExternalProfile $profile, ExternalEntry $entry, ForceDeleteEntryAction $action): JsonResponse
    {
        $message = $action->forceDelete($entry);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}