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
use App\Http\Resources\List\Resource\ExternalProfileJsonResource;
use App\Models\List\ExternalProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class ExternalProfileController extends BaseController
{
    public function __construct()
    {
        parent::__construct(ExternalProfile::class, 'externalprofile');

        $this->middleware(EnabledOnlyOnLocalhost::class);
        $this->middleware(EnsureFeaturesAreActive::using(AllowExternalProfileManagement::class))->except(['index', 'show']);
        $this->middleware(UserExceedsExternalProfileLimit::class)->only(['store', 'restore']);
    }

    public function index(IndexRequest $request, IndexAction $action): ExternalProfileCollection
    {
        $query = new Query($request->validated());

        $builder = ExternalProfile::query()->where(ExternalProfile::ATTRIBUTE_VISIBILITY, ExternalProfileVisibility::PUBLIC->value);

        $user = Auth::user();
        if ($user) {
            $builder->orWhereBelongsTo($user);
        }

        $externalprofiles = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index($builder, $query, $request->schema());

        return new ExternalProfileCollection($externalprofiles, $query);
    }

    public function show(ShowRequest $request, ExternalProfile $externalprofile, ShowAction $action): ExternalProfileJsonResource
    {
        $query = new Query($request->validated());

        $show = $action->show($externalprofile, $query, $request->schema());

        return new ExternalProfileJsonResource($show, $query);
    }

    /**
     * @param  StoreAction<ExternalProfile>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): ExternalProfileJsonResource
    {
        $validated = array_merge(
            $request->validated(),
            [ExternalProfile::ATTRIBUTE_USER => Auth::id()]
        );

        $profile = $action->store(ExternalProfile::query(), $validated);

        return new ExternalProfileJsonResource($profile, new Query());
    }

    public function update(UpdateRequest $request, ExternalProfile $externalprofile, UpdateAction $action): ExternalProfileJsonResource
    {
        $updated = $action->update($externalprofile, $request->validated());

        return new ExternalProfileJsonResource($updated, new Query());
    }

    public function destroy(ExternalProfile $externalprofile, DestroyAction $action): JsonResponse
    {
        $message = $action->forceDelete($externalprofile);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
