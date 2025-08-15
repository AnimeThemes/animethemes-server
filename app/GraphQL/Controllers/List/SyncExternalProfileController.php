<?php

declare(strict_types=1);

namespace App\GraphQL\Controllers\List;

use App\Exceptions\GraphQL\ClientForbiddenException;
use App\GraphQL\Controllers\BaseController;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Arr;

class SyncExternalProfileController extends BaseController
{
    /**
     * Start a new sync job.
     *
     * @param  null  $root
     * @param  array<string, mixed>  $args
     */
    public function store($root, array $args): array
    {
        /** @var ExternalProfile $profile */
        $profile = Arr::pull($args, self::MODEL);

        if (! $profile->canBeSynced()) {
            throw new ClientForbiddenException('This external profile cannot be synced at the moment.');
        }

        $profile->dispatchSyncJob();

        return [
            'message' => 'Job dispatched.',
        ];
    }
}
