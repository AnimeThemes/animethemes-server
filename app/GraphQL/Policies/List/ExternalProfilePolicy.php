<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\List;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\GraphQL\Controllers\List\SyncExternalProfileController;
use App\GraphQL\Policies\BasePolicy;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Arr;

class ExternalProfilePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  array|null  $injected
     * @param  string|null  $keyName
     * @return bool
     */
    public function view(?User $user, ?array $injected = null, ?string $keyName = SyncExternalProfileController::ROUTE_SLUG): bool
    {
        /** @var ExternalProfile $profile */
        $profile = Arr::get($injected, $keyName);

        if ($user !== null) {
            return ($profile->user()->is($user) || $profile->visibility !== ExternalProfileVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(ExternalProfile::class));
        }

        return $profile->visibility !== ExternalProfileVisibility::PRIVATE;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  array  $injected
     * @param  string|null  $keyName
     * @return bool
     */
    public function update(User $user, array $injected, ?string $keyName = SyncExternalProfileController::ROUTE_SLUG): bool
    {
        /** @var ExternalProfile $profile */
        $profile = Arr::get($injected, $keyName);

        return $profile->user()->is($user) && parent::update($user, $injected, $keyName);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  array  $injected
     * @param  string|null  $keyName
     * @return bool
     */
    public function delete(User $user, array $injected, ?string $keyName = SyncExternalProfileController::ROUTE_SLUG): bool
    {
        /** @var ExternalProfile $profile */
        $profile = Arr::get($injected, $keyName);

        return $profile->user()->is($user) && parent::update($user, $injected, $keyName);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  array  $injected
     * @param  string|null  $keyName
     * @return bool
     */
    public function restore(User $user, array $injected, ?string $keyName = SyncExternalProfileController::ROUTE_SLUG): bool
    {
        /** @var ExternalProfile $profile */
        $profile = Arr::get($injected, $keyName);

        return $profile->user()->is($user) && parent::update($user, $injected, $keyName);
    }
}
