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
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class ExternalProfilePolicy extends BasePolicy
{
    public function viewAny(?User $user, mixed $value = null): Response
    {
        if (Filament::isServing()) {
            return $user?->hasRole(Role::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        return $user?->can(CrudPermission::VIEW->format(ExternalProfile::class))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  ExternalProfile  $profile
     */
    public function view(?User $user, Model $profile): Response
    {
        if (Filament::isServing()) {
            return $user?->hasRole(Role::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        if ($user instanceof User) {
            return ($profile->user()->is($user) || $profile->visibility !== ExternalProfileVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(ExternalProfile::class))
                ? Response::allow()
                : Response::deny();
        }

        return $profile->visibility !== ExternalProfileVisibility::PRIVATE
            ? Response::allow()
            : Response::deny();
    }

    public function create(User $user): Response
    {
        if (Filament::isServing()) {
            return $user->hasRole(Role::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        return parent::create($user);
    }

    /**
     * @param  ExternalProfile  $profile
     */
    public function update(User $user, Model $profile): Response
    {
        if (Filament::isServing()) {
            return $user->hasRole(Role::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        return $profile->user()->is($user) && parent::update($user, $profile)->allowed()
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  ExternalProfile  $profile
     */
    public function delete(User $user, Model $profile): Response
    {
        if (Filament::isServing()) {
            return $user->hasRole(Role::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        return $profile->user()->is($user) && parent::delete($user, $profile)->allowed()
            ? Response::allow()
            : Response::deny();
    }

    public function addExternalEntry(User $user): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }
}
