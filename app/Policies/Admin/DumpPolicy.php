<?php

declare(strict_types=1);

namespace App\Policies\Admin;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DumpPolicy.
 */
class DumpPolicy extends BasePolicy
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
            return $user !== null && $user->can(CrudPermission::VIEW->format(static::getModel()));
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  Dump  $dump
     * @return bool
     */
    public function view(?User $user, BaseModel|Model $dump): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->can(CrudPermission::VIEW->format(static::getModel()));
        }

        if ($user?->hasRole(Role::ADMIN->value)) {
            return true;
        }

        return str($dump->path)->contains(Dump::safeDumps());
    }
}
