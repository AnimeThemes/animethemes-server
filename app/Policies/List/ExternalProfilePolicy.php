<?php

declare(strict_types=1);

namespace App\Policies\List;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\ExternalProfile;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExternalProfilePolicy.
 */
class ExternalProfilePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole('Admin');
        }

        return $user === null || $user->can(CrudPermission::VIEW->format(ExternalProfile::class));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  ExternalProfile  $profile
     * @return bool
     */
    public function view(?User $user, BaseModel|Model $profile): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole('Admin');
        }

        return $user !== null
            ? ($user->getKey() === $profile->user_id || ExternalProfileVisibility::PRIVATE !== $profile->visibility) && $user->can(CrudPermission::VIEW->format(ExternalProfile::class))
            : ExternalProfileVisibility::PRIVATE !== $profile->visibility;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole('Admin');
        }

        return $user->can(CrudPermission::CREATE->format(ExternalProfile::class));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  ExternalProfile  $profile
     * @return bool
     */
    public function update(User $user, BaseModel|Model $profile): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole('Admin');
        }

        return !$profile->trashed() && $user->getKey() === $profile->user_id && $user->can(CrudPermission::UPDATE->format(ExternalProfile::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  ExternalProfile  $profile
     * @return bool
     */
    public function delete(User $user, BaseModel|Model $profile): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole('Admin');
        }

        return !$profile->trashed() && $user->getKey() === $profile->user_id && $user->can(CrudPermission::DELETE->format(ExternalProfile::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  ExternalProfile  $profile
     * @return bool
     */
    public function restore(User $user, BaseModel|Model $profile): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole('Admin');
        }

        return $profile->trashed() && $user->getKey() === $profile->user_id && $user->can(ExtendedCrudPermission::RESTORE->format(ExternalProfile::class));
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
