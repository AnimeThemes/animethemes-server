<?php

declare(strict_types=1);

namespace App\Policies\List;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use Illuminate\Auth\Access\HandlesAuthorization;
use Laravel\Nova\Nova;

/**
 * Class ExternalProfilePolicy.
 */
class ExternalProfilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        return true;
        return Nova::whenServing(
            fn (): bool => $user !== null && $user->hasRole('Admin'),
            fn (): bool => $user === null || $user->can(CrudPermission::VIEW->format(ExternalProfile::class))
        );
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  ExternalProfile  $profile
     * @return bool
     */
    public function view(?User $user, ExternalProfile $profile): bool
    {
        return true;
        return Nova::whenServing(
            fn (): bool => $user !== null && $user->hasRole('Admin'),
            fn (): bool => $user !== null
                ? ($user->getKey() === $profile->user_id || ExternalProfileVisibility::PRIVATE !== $profile->visibility) && $user->can(CrudPermission::VIEW->format(ExternalProfile::class))
                : ExternalProfileVisibility::PRIVATE !== $profile->visibility
        );
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return Nova::whenServing(
            fn (): bool => $user->hasRole('Admin'),
            fn (): bool => $user->can(CrudPermission::CREATE->format(ExternalProfile::class))
        );
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  ExternalProfile  $profile
     * @return bool
     */
    public function update(User $user, ExternalProfile $profile): bool
    {
        return Nova::whenServing(
            fn (): bool => $user->hasRole('Admin'),
            fn (): bool => ! $profile->trashed() && $user->getKey() === $profile->user_id && $user->can(CrudPermission::UPDATE->format(ExternalProfile::class))
        );
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  ExternalProfile  $profile
     * @return bool
     */
    public function delete(User $user, ExternalProfile $profile): bool
    {
        return Nova::whenServing(
            fn (): bool => $user->hasRole('Admin'),
            fn (): bool => ! $profile->trashed() && $user->getKey() === $profile->user_id && $user->can(CrudPermission::DELETE->format(ExternalProfile::class))
        );
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  ExternalProfile  $profile
     * @return bool
     */
    public function restore(User $user, ExternalProfile $profile): bool
    {
        return Nova::whenServing(
            fn (): bool => $user->hasRole('Admin'),
            fn (): bool => $profile->trashed() && $user->getKey() === $profile->user_id && $user->can(ExtendedCrudPermission::RESTORE->format(ExternalProfile::class))
        );
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(ExternalProfile::class));
    }

    /**
     * Determine whether the user can add a entry to the profile.
     *
     * @param  User  $user
     * @return bool
     */
    public function addEntry(User $user): bool
    {
        return $user->hasRole('Admin');
    }
}
