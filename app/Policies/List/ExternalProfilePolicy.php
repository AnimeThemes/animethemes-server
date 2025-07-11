<?php

declare(strict_types=1);

namespace App\Policies\List;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role;
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
            return $user !== null && $user->hasRole(Role::ADMIN->value);
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
            return $user !== null && $user->hasRole(Role::ADMIN->value);
        }

        if ($user !== null) {
            return ($profile->user()->is($user) || $profile->visibility !== ExternalProfileVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(ExternalProfile::class));
        }

        return $profile->visibility !== ExternalProfileVisibility::PRIVATE;
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
            return $user->hasRole(Role::ADMIN->value);
        }

        return parent::create($user);
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
            return $user->hasRole(Role::ADMIN->value);
        }

        return $profile->user()->is($user) && parent::update($user, $profile);
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
            return $user->hasRole(Role::ADMIN->value);
        }

        return $profile->user()->is($user) && parent::delete($user, $profile);
    }

    /**
     * Determine whether the user can add a entry to the profile.
     *
     * @param  User  $user
     * @return bool
     */
    public function addExternalEntry(User $user): bool
    {
        return $user->hasRole(Role::ADMIN->value);
    }
}
