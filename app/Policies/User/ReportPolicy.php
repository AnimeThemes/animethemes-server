<?php

declare(strict_types=1);

namespace App\Policies\User;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role;
use App\Models\Auth\User;
use App\Models\User\Report;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class ReportPolicy extends BasePolicy
{
    public function viewAny(?User $user): Response
    {
        if ($user !== null && $user->hasRole(Role::ADMIN->value)) {
            return Response::allow();
        }

        return Response::allow();
    }

    /**
     * @param  Report  $report
     */
    public function view(?User $user, Model $report): Response
    {
        if ($user !== null && $user->hasRole(Role::ADMIN->value)) {
            return Response::allow();
        }

        return $report->user()->is($user) && $user?->can(CrudPermission::VIEW->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Report  $report
     */
    public function update(User $user, Model $report): Response
    {
        if ($user->hasRole(Role::ADMIN->value)) {
            return Response::allow();
        }

        return $report->user()->is($user) && $user->can(CrudPermission::UPDATE->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Report  $report
     */
    public function delete(User $user, Model $report): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }
}
