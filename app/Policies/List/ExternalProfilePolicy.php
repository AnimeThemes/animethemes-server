<?php

declare(strict_types=1);

namespace App\Policies\List;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class ExternalProfilePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
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
     * @param  ExternalProfile  $profile
     */
    public function view(?User $user, Model $profile): bool
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
     * @param  ExternalProfile  $profile
     */
    public function update(User $user, Model $profile): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole(Role::ADMIN->value);
        }

        return $profile->user()->is($user) && parent::update($user, $profile);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  ExternalProfile  $profile
     */
    public function delete(User $user, Model $profile): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole(Role::ADMIN->value);
        }

        return $profile->user()->is($user) && parent::delete($user, $profile);
    }

    /**
     * Determine whether the user can add a entry to the profile.
     */
    public function addExternalEntry(User $user): bool
    {
        return $user->hasRole(Role::ADMIN->value);
    }
}
