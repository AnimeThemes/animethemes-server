<?php

declare(strict_types=1);

namespace App\Policies\Admin;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class DumpPolicy extends BasePolicy
{
    /**
     * @param  Dump  $dump
     */
    public function view(?User $user, Model $dump): Response
    {
        return $user?->hasRole(Role::ADMIN->value) && $user->can(CrudPermission::VIEW->format(static::getModel()))
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
