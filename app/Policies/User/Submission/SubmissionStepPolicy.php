<?php

declare(strict_types=1);

namespace App\Policies\User\Submission;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role;
use App\Models\Auth\User;
use App\Models\User\Submission\SubmissionStep;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class SubmissionStepPolicy extends BasePolicy
{
    public function viewAny(?User $user, mixed $value = null): Response
    {
        if (Filament::isServing()) {
            return $user instanceof User && $user->hasRole(Role::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        return Response::allow();
    }

    /**
     * @param  SubmissionStep  $step
     */
    public function view(?User $user, Model $step): Response
    {
        if (Filament::isServing()) {
            return $user instanceof User && $user->hasRole(Role::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        return $step->submission->user()->is($user) && $user?->can(CrudPermission::VIEW->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  SubmissionStep  $step
     */
    public function update(User $user, Model $step): Response
    {
        if ($user->hasRole(Role::ADMIN->value)) {
            return Response::allow();
        }

        return $step->submission->user()->is($user) && $user->can(CrudPermission::UPDATE->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  SubmissionStep  $step
     */
    public function delete(User $user, Model $step): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }
}
