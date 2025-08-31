<?php

declare(strict_types=1);

namespace App\Policies\User\Report;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role;
use App\Models\Auth\User;
use App\Models\User\Report\ReportStep;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class ReportStepPolicy extends BasePolicy
{
    public function viewAny(?User $user, mixed $value = null): Response
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole(Role::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        return Response::allow();
    }

    /**
     * @param  ReportStep  $step
     */
    public function view(?User $user, Model $step): Response
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole(Role::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        return $step->report->user()->is($user) && $user?->can(CrudPermission::VIEW->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  ReportStep  $step
     */
    public function update(User $user, Model $step): Response
    {
        if ($user->hasRole(Role::ADMIN->value)) {
            return Response::allow();
        }

        return $step->report->user()->is($user) && $user->can(CrudPermission::UPDATE->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  ReportStep  $step
     */
    public function delete(User $user, Model $step): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }
}
