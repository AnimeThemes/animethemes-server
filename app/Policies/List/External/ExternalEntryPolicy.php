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
use Illuminate\Database\Eloquent\Model;

class ExternalEntryPolicy extends BasePolicy
{
    public function viewAny(?User $user): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole(Role::ADMIN->value);
        }

        /** @var ExternalProfile|null $profile */
        $profile = request()->route('externalprofile');

        if ($user !== null) {
            return ($profile?->user()->is($user) || $profile?->visibility !== ExternalProfileVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(ExternalEntry::class));
        }

        return $profile?->visibility !== ExternalProfileVisibility::PRIVATE;
    }

    /**
     * @param  ExternalEntry  $entry
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function view(?User $user, Model $entry): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole(Role::ADMIN->value);
        }

        /** @var ExternalProfile|null $profile */
        $profile = request()->route('externalprofile');

        if ($user !== null) {
            return ($profile?->user()->is($user) || $profile?->visibility !== ExternalProfileVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(ExternalEntry::class));
        }

        return $profile?->visibility !== ExternalProfileVisibility::PRIVATE;
    }

    public function create(User $user): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole(Role::ADMIN->value);
        }

        return false;
    }

    /**
     * @param  ExternalEntry  $entry
     */
    public function update(User $user, Model $entry): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole(Role::ADMIN->value);
        }

        return false;
    }

    /**
     * @param  ExternalEntry  $entry
     */
    public function delete(User $user, Model $entry): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole(Role::ADMIN->value);
        }

        return false;
    }
}
