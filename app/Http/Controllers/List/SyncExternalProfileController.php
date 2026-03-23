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
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

#[Middleware(EnabledOnlyOnLocalhost::class)]
#[Middleware(EnsureFeaturesAreActive::using(AllowExternalProfileManagement::class), except: ['show'])]
#[Middleware(ExternalProfileSyncLimit::class, ['update'])]
class SyncExternalProfileController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ExternalProfile::class, 'externalprofile');
    }

    /**
     * Display the current progress status of the sync.
     */
    public function show(ShowRequest $request, ExternalProfile $externalprofile): void
    {
        // TODO
    }

    public function update(ExternalProfile $externalProfile): JsonResponse
    {
        $externalProfile->dispatchSyncJob();

        return new JsonResponse([
            'message' => 'Job dispatched.',
        ], 201);
    }
}
