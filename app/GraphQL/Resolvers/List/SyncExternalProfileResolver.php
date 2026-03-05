<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers\List;

use App\Exceptions\GraphQL\ClientForbiddenException;
use App\Features\AllowExternalProfileManagement;
use App\GraphQL\Resolvers\BaseResolver;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Arr;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class SyncExternalProfileResolver extends BaseResolver
{
    public function __construct()
    {
        $this->middleware(EnabledOnlyOnLocalhost::class);
        $this->middleware(EnsureFeaturesAreActive::using(AllowExternalProfileManagement::class))->only(['update']);
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function update(array $args): array
    {
        $this->runMiddleware();

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
