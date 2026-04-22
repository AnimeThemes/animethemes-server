<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\List;

use App\Concerns\GraphQL\RunMiddlewares;
use App\Features\AllowExternalProfileManagement;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Arr;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;
use Nuwave\Lighthouse\Exceptions\AuthorizationException;

class SyncExternalProfileMutation
{
    use RunMiddlewares;

    /**
     * @param  array<string, mixed>  $args
     */
    public function __invoke(null $_, array $args): array
    {
        $this->runHttpMiddleware([
            EnabledOnlyOnLocalhost::class,
            EnsureFeaturesAreActive::using(AllowExternalProfileManagement::class),
        ]);

        $profile = ExternalProfile::query()->find(Arr::pull($args, 'id'));

        throw_unless(
            $profile->canBeSynced(),
            AuthorizationException::class,
            'This external profile cannot be synced at the moment.'
        );

        $profile->dispatchSyncJob();

        return [
            'message' => 'Job dispatched.',
        ];
    }
}
