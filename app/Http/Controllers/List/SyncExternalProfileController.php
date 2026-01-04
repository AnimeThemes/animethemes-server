<?php

declare(strict_types=1);

namespace App\Http\Controllers\List;

use App\Features\AllowExternalProfileManagement;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;
use App\Http\Middleware\Models\List\ExternalProfileSyncLimit;
use App\Http\Requests\Api\ShowRequest;
use App\Models\List\ExternalProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class SyncExternalProfileController extends Controller
{
    public function __construct()
    {
        $isExternalProfileManagementAllowed = Str::of(EnsureFeaturesAreActive::class)
            ->append(':')
            ->append(AllowExternalProfileManagement::class)
            ->__toString();

        $this->authorizeResource(ExternalProfile::class, 'externalprofile');
        $this->middleware(EnabledOnlyOnLocalhost::class);
        $this->middleware($isExternalProfileManagementAllowed)->except(['show']);
        $this->middleware(ExternalProfileSyncLimit::class)->only('update');
    }

    /**
     * Display the current progress status of the sync.
     */
    public function show(ShowRequest $request, ExternalProfile $externalprofile): void
    {
        // TODO
    }

    /**
     * Start a new sync job.
     */
    public function update(ExternalProfile $externalProfile): JsonResponse
    {
        $externalProfile->dispatchSyncJob();

        return new JsonResponse([
            'message' => 'Job dispatched.',
        ], 201);
    }
}
