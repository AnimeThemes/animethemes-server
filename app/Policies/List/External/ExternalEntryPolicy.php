<?php

declare(strict_types=1);

namespace App\Policies\List\External;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Models\Auth\User;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class ExternalEntryPolicy extends BasePolicy
{
    /**
     * @param  ExternalProfile  $profile
     */
    public function viewAny(?User $user, $profile = null): Response
    {
        if (Filament::isServing()) {
            return $user?->hasRole(Role::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        /** @var ExternalProfile|null $profile */
        $profile ??= request()->route('externalprofile');

        if ($user instanceof User) {
            return ($profile?->user()->is($user) || $profile?->visibility !== ExternalProfileVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(ExternalEntry::class))
                ? Response::allow()
                : Response::deny();
        }

        return $profile?->visibility !== ExternalProfileVisibility::PRIVATE
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  ExternalEntry  $entry
     * @param  ExternalProfile  $profile
     */
    public function view(?User $user, Model $entry, $profile = null): Response
    {
        if (Filament::isServing()) {
            return $user?->hasRole(Role::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        if ($user instanceof User) {
            return ($profile?->user()->is($user) || $profile?->visibility !== ExternalProfileVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(ExternalEntry::class))
                ? Response::allow()
                : Response::deny();
        }

        return $profile?->visibility !== ExternalProfileVisibility::PRIVATE
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

        return Response::deny();
    }

    /**
     * @param  ExternalEntry  $entry
     */
    public function update(User $user, Model $entry): Response
    {
        if (Filament::isServing()) {
            return $user->hasRole(Role::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        return Response::deny();
    }

    /**
     * @param  ExternalEntry  $entry
     */
    public function delete(User $user, Model $entry): Response
    {
        if (Filament::isServing()) {
            return $user->hasRole(Role::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        return Response::deny();
    }
}
