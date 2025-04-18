<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\List\ExternalProfile;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\GraphQL\Policies\BasePolicy;
use App\Models\Auth\User;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Arr;

/**
 * Class ExternalEntryPolicy.
 */
class ExternalEntryPolicy extends BasePolicy
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
        /** @var ExternalProfile|null $profile */
        $profile = Arr::get($injected, 'profile');

        return $user !== null
            ? ($profile?->user()->is($user) || ExternalProfileVisibility::PRIVATE !== $profile?->visibility) && $user->can(CrudPermission::VIEW->format(ExternalEntry::class))
            : ExternalProfileVisibility::PRIVATE !== $profile?->visibility;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  array|null  $injected
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function view(?User $user, ?array $injected = null): bool
    {
        /** @var ExternalProfile|null $profile */
        $profile = Arr::get($injected, 'profile');

        return $user !== null
            ? ($profile?->user()->is($user) || ExternalProfileVisibility::PRIVATE !== $profile?->visibility) && $user->can(CrudPermission::VIEW->format(ExternalEntry::class))
            : ExternalProfileVisibility::PRIVATE !== $profile?->visibility;
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
        /** @var ExternalProfile|null $profile */
        $profile = Arr::get($injected, 'profile');

        return $profile?->user()->is($user);
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
        /** @var ExternalProfile|null $profile */
        $profile = Arr::get($injected, 'profile');
        /** @var ExternalEntry $entry */
        $entry = Arr::get($injected, 'id');

        return !$entry->trashed() && $profile?->user()->is($user) && $user->can(CrudPermission::UPDATE->format(ExternalEntry::class));
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
        /** @var ExternalProfile|null $profile */
        $profile = Arr::get($injected, 'profile');
        /** @var ExternalEntry $entry */
        $entry = Arr::get($injected, 'id');

        return !$entry->trashed() && $profile?->user()->is($user) && $user->can(CrudPermission::DELETE->format(ExternalEntry::class));
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
        /** @var ExternalProfile|null $profile */
        $profile = Arr::get($injected, 'profile');
        /** @var ExternalEntry $entry */
        $entry = Arr::get($injected, 'id');

        return $entry->trashed() && $profile?->user()->is($user) && $user->can(ExtendedCrudPermission::RESTORE->format(ExternalEntry::class));
    }
}
