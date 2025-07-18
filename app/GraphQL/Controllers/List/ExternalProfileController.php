<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\List;

use App\GraphQL\Controllers\BaseController;
use App\Models\List\ExternalProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

/**
 * Class ExternalProfileController.
 */
class ExternalProfileController extends BaseController
{
    final public const ROUTE_SLUG = 'id';

    /**
     * Start a new sync job.
     *
     * @param  null  $_
     * @param  array  $args
     * @return JsonResponse
     */
    public function sync($_, array $args): JsonResponse
    {
        /** @var ExternalProfile $profile */
        $profile = Arr::pull($args, self::ROUTE_SLUG);

        if (! $profile->canBeSynced()) {
            return new JsonResponse([
                'error' => 'This external profile cannot be synced at the moment.',
            ], 403);
        }

        $profile->startSyncJob();

        return new JsonResponse([
            'message' => 'Job dispatched.',
        ], 201);
    }
}
