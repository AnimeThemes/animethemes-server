<?php

declare(strict_types=1);

namespace App\Policies\User;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\User\Report;
use App\Policies\BasePolicy;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ReportPolicy.
 */
class ReportPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        if ($user !== null && $user->hasRole(Role::ADMIN->value)) {
            return true;
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  Report  $report
     * @return bool
     */
    public function view(?User $user, BaseModel|Model $report): bool
    {
        if ($user !== null && $user->hasRole(Role::ADMIN->value)) {
            return true;
        }

        return $user !== null
            ? $report->user()->is($user) && $user->can(CrudPermission::VIEW->format(static::getModel()))
            : false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Report  $report
     * @return bool
     */
    public function update(User $user, BaseModel|Model $report): bool
    {
        if ($user->hasRole(Role::ADMIN->value)) {
            return true;
        }

        return $report->user()->is($user) && $user->can(CrudPermission::UPDATE->format(static::getModel()));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Report  $report
     * @return bool
     */
    public function delete(User $user, BaseModel|Model $report): bool
    {
        return $user->hasRole(Role::ADMIN->value);
    }
}
