<?php

declare(strict_types=1);

namespace App\Policies\User;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role;
use App\Models\Auth\User;
use App\Models\User\Submission;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class SubmissionPolicy extends BasePolicy
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
     * @param  Submission  $submission
     */
    public function view(?User $user, Model $submission): Response
    {
        if (Filament::isServing()) {
            return $user instanceof User && $user->hasRole(Role::ADMIN->value)
                ? Response::allow()
                : Response::deny();
        }

        return $submission->user()->is($user) && $user?->can(CrudPermission::VIEW->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Submission  $submission
     */
    public function update(User $user, Model $submission): Response
    {
        if ($user->hasRole(Role::ADMIN->value)) {
            return Response::allow();
        }

        return ! $submission->isLocked() && $submission->user()->is($user) && $user->can(CrudPermission::UPDATE->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Submission  $submission
     */
    public function delete(User $user, Model $submission): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }
}
