<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers\List;

use App\Exceptions\GraphQL\ClientForbiddenException;
use App\GraphQL\Resolvers\BaseResolver;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Arr;

class SyncExternalProfileResolver extends BaseResolver
{
    /**
     * Start a new sync job.
     *
     * @param  array<string, mixed>  $args
     */
    public function store($root, array $args): array
    {
        /** @var ExternalProfile $profile */
        $profile = Arr::pull($args, self::MODEL);

        throw_unless(
            $profile->canBeSynced(),
            ClientForbiddenException::class,
            'This external profile cannot be synced at the moment.'
        );

        $profile->dispatchSyncJob();

        return [
            'message' => 'Job dispatched.',
        ];
    }
}
