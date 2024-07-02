<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List\External;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\ForceDeleteAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\RestoreAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\StoreAction;
use App\Actions\Http\Api\UpdateAction;
use App\Features\AllowExternalProfileManagement;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;
use App\Http\Middleware\Models\List\ExternalProfileExceedsEntryLimit;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\List\External\Collection\ExternalEntryCollection;
use App\Http\Resources\List\External\Resource\ExternalEntryResource;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

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

        $isExternalProfileManagementAllowed = Str::of(EnsureFeaturesAreActive::class)
            ->append(':')
            ->append(AllowExternalProfileManagement::class)
            ->__toString();

        $this->middleware(EnabledOnlyOnLocalhost::class);
        $this->middleware($isExternalProfileManagementAllowed)->except(['index', 'show']);
        $this->middleware(ExternalProfileExceedsEntryLimit::class)->only(['store', 'restore']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  ExternalProfile  $externalprofile
     * @param  IndexAction  $action
     * @return ExternalEntryCollection
     */
    public function index(IndexRequest $request, ExternalProfile $externalprofile, IndexAction $action): ExternalEntryCollection
    {
        $query = new Query($request->validated());

        $builder = ExternalEntry::query()->where(ExternalEntry::ATTRIBUTE_EXTERNAL_PROFILE, $externalprofile->getKey());

        $resources = $action->index($builder, $query, $request->schema());

        return new ExternalEntryCollection($resources, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  ExternalProfile  $externalprofile
     * @param  StoreAction  $action
     * @return ExternalEntryResource
     */
    public function store(StoreRequest $request, ExternalProfile $externalprofile, StoreAction $action): ExternalEntryResource
    {
        $validated = array_merge(
            $request->validated(),
            [ExternalEntry::ATTRIBUTE_EXTERNAL_PROFILE => $externalprofile->getKey()]
        );

        $externalentry = $action->store(ExternalEntry::query(), $validated);

        return new ExternalEntryResource($externalentry, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  ExternalProfile  $externalprofile
     * @param  ExternalEntry  $externalentry
     * @param  ShowAction  $action
     * @return ExternalEntryResource
     */
    public function show(ShowRequest $request, ExternalProfile $externalprofile, ExternalEntry $externalentry, ShowAction $action): ExternalEntryResource
    {
        $query = new Query($request->validated());

        $show = $action->show($externalentry, $query, $request->schema());

        return new ExternalEntryResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  ExternalProfile  $externalprofile
     * @param  ExternalEntry  $externalentry
     * @param  UpdateAction  $action
     * @return ExternalEntryResource
     */
    public function update(UpdateRequest $request, ExternalProfile $externalprofile, ExternalEntry $externalentry, UpdateAction $action): ExternalEntryResource
    {
        $updated = $action->update($externalentry, $request->validated());

        return new ExternalEntryResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  ExternalProfile  $externalprofile
     * @param  ExternalEntry  $externalentry
     * @param  DestroyAction  $action
     * @return ExternalEntryResource
     */
    public function destroy(ExternalProfile $externalprofile, ExternalEntry $externalentry, DestroyAction $action): ExternalEntryResource
    {
        $deleted = $action->destroy($externalentry);

        return new ExternalEntryResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  ExternalProfile  $externalprofile
     * @param  ExternalEntry  $externalentry
     * @param  RestoreAction  $action
     * @return ExternalEntryResource
     */
    public function restore(ExternalProfile $externalprofile, ExternalEntry $externalentry, RestoreAction $action): ExternalEntryResource
    {
        $restored = $action->restore($externalentry);

        return new ExternalEntryResource($restored, new Query());
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  ExternalProfile  $externalprofile
     * @param  ExternalEntry  $externalentry
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(ExternalProfile $externalprofile, ExternalEntry $externalentry, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($externalentry);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}