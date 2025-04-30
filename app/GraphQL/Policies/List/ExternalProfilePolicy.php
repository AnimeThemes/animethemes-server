<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\List;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\GraphQL\Policies\BasePolicy;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Arr;

/**
 * Class ExternalProfilePolicy.
 */
class ExternalProfilePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  User|null  $user
     * @param  array|null  $injected
     * @return bool
     */
    public function viewAny(?User $user, ?array $injected = null): bool
    {
        return $user === null || $user->can(CrudPermission::VIEW->format(ExternalProfile::class));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  array|null  $injected
     * @return bool
     */
    public function view(?User $user, ?array $injected = null): bool
    {
        /** @var ExternalProfile $profile */
        $profile = Arr::get($injected, 'id');

        return $user !== null
            ? ($profile->user()->is($user) || ExternalProfileVisibility::PRIVATE !== $profile->visibility) && $user->can(CrudPermission::VIEW->format(ExternalProfile::class))
            : ExternalProfileVisibility::PRIVATE !== $profile->visibility;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @param  array|null  $injected
     * @return bool
     */
    public function create(User $user, ?array $injected = null): bool
    {
        return $user->can(CrudPermission::CREATE->format(ExternalProfile::class));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  array  $injected
     * @return bool
     */
    public function update(User $user, array $injected): bool
    {
        /** @var ExternalProfile $profile */
        $profile = Arr::get($injected, 'id');

        return !$profile->trashed() && $profile->user()->is($user) && $user->can(CrudPermission::UPDATE->format(ExternalProfile::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  array  $injected
     * @return bool
     */
    public function delete(User $user, array $injected): bool
    {
        /** @var ExternalProfile $profile */
        $profile = Arr::get($injected, 'id');

        return !$profile->trashed() && $profile->user()->is($user) && $user->can(CrudPermission::DELETE->format(ExternalProfile::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  array  $injected
     * @return bool
     */
    public function restore(User $user, array $injected): bool
    {
        /** @var ExternalProfile $profile */
        $profile = Arr::get($injected, 'id');

        return $profile->trashed() && $profile->user()->is($user) && $user->can(ExtendedCrudPermission::RESTORE->format(ExternalProfile::class));
    }
}
