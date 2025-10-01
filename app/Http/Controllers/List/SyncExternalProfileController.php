<?php

declare(strict_types=1);

namespace App\Http\Controllers\List;

use App\Features\AllowExternalProfileManagement;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;
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

        $this->middleware(EnabledOnlyOnLocalhost::class);
        $this->middleware($isExternalProfileManagementAllowed)->except(['show']);
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
    public function store(ExternalProfile $externalProfile): JsonResponse
    {
        if (! $externalProfile->canBeSynced()) {
            return new JsonResponse([
                'error' => 'This external profile cannot be synced at the moment.',
            ], 403);
        }

        $externalProfile->dispatchSyncJob();

        return new JsonResponse([
            'message' => 'Job dispatched.',
        ], 201);
    }
}
