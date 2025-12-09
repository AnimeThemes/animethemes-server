<?php

declare(strict_types=1);

namespace App\Policies\User\Submission;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role;
use App\Models\Auth\User;
use App\Models\User\Submission\SubmissionStage;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class SubmissionStagePolicy extends BasePolicy
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
     * @param  SubmissionStage  $stage
     */
    public function view(?User $user, Model $stage): Response
    {
        if (Filament::isServing()) {
            return $user instanceof User && $user->hasRole(Role::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        return $stage->submission->user()->is($user) && $user?->can(CrudPermission::VIEW->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  SubmissionStage  $stage
     */
    public function update(User $user, Model $stage): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  SubmissionStage  $stage
     */
    public function delete(User $user, Model $stage): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }
}
