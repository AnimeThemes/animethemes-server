<?php

declare(strict_types=1);

namespace App\Policies\User;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role;
use App\Models\Auth\User;
use App\Models\User\Report;
use App\Policies\BasePolicy;
use Illuminate\Database\Eloquent\Model;

class ReportPolicy extends BasePolicy
{
    public function viewAny(?User $user): bool
    {
        if ($user !== null && $user->hasRole(Role::ADMIN->value)) {
            return true;
        }

        return true;
    }

    /**
     * @param  Report  $report
     */
    public function view(?User $user, Model $report): bool
    {
        if ($user !== null && $user->hasRole(Role::ADMIN->value)) {
            return true;
        }

        return $user !== null
            ? $report->user()->is($user) && $user->can(CrudPermission::VIEW->format(static::getModel()))
            : false;
    }

    /**
     * @param  Report  $report
     */
    public function update(User $user, Model $report): bool
    {
        if ($user->hasRole(Role::ADMIN->value)) {
            return true;
        }

        return $report->user()->is($user) && $user->can(CrudPermission::UPDATE->format(static::getModel()));
    }

    /**
     * @param  Report  $report
     */
    public function delete(User $user, Model $report): bool
    {
        return $user->hasRole(Role::ADMIN->value);
    }
}
