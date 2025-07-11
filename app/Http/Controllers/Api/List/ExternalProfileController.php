<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\StoreAction;
use App\Actions\Http\Api\UpdateAction;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Features\AllowExternalProfileManagement;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;
use App\Http\Middleware\Models\List\UserExceedsExternalProfileLimit;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\List\Collection\ExternalProfileCollection;
use App\Http\Resources\List\Resource\ExternalProfileResource;
use App\Models\List\ExternalProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

/**
 * Class ExternalProfileController.
 */
class ExternalProfileController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(ExternalProfile::class, 'externalprofile');

        $isExternalProfileManagementAllowed = Str::of(EnsureFeaturesAreActive::class)
            ->append(':')
            ->append(AllowExternalProfileManagement::class)
            ->__toString();

        $this->middleware(EnabledOnlyOnLocalhost::class);
        $this->middleware($isExternalProfileManagementAllowed)->except(['index', 'show']);
        $this->middleware(UserExceedsExternalProfileLimit::class)->only(['store', 'restore']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return ExternalProfileCollection
     */
    public function index(IndexRequest $request, IndexAction $action): ExternalProfileCollection
    {
        $query = new Query($request->validated());

        $builder = ExternalProfile::query()->where(ExternalProfile::ATTRIBUTE_VISIBILITY, ExternalProfileVisibility::PUBLIC->value);

        $userId = Auth::id();
        if ($userId) {
            $builder->orWhereBelongsTo(Auth::user());
        }

        $externalprofiles = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index($builder, $query, $request->schema());

        return new ExternalProfileCollection($externalprofiles, $query);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  ExternalProfile  $externalprofile
     * @param  ShowAction  $action
     * @return ExternalProfileResource
     */
    public function show(ShowRequest $request, ExternalProfile $externalprofile, ShowAction $action): ExternalProfileResource
    {
        $query = new Query($request->validated());

        $show = $action->show($externalprofile, $query, $request->schema());

        return new ExternalProfileResource($show, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction  $action
     * @return ExternalProfileResource
     */
    public function store(StoreRequest $request, StoreAction $action): ExternalProfileResource
    {
        $validated = array_merge(
            $request->validated(),
            [ExternalProfile::ATTRIBUTE_USER => Auth::id()]
        );

        $profile = $action->store(ExternalProfile::query(), $validated);

        return new ExternalProfileResource($profile, new Query());
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  ExternalProfile  $externalprofile
     * @param  UpdateAction  $action
     * @return ExternalProfileResource
     */
    public function update(UpdateRequest $request, ExternalProfile $externalprofile, UpdateAction $action): ExternalProfileResource
    {
        $updated = $action->update($externalprofile, $request->validated());

        return new ExternalProfileResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  ExternalProfile  $externalprofile
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(ExternalProfile $externalprofile, DestroyAction $action): JsonResponse
    {
        $message = $action->forceDelete($externalprofile);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
