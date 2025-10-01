<?php

declare(strict_types=1);

namespace App\Policies\Admin;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DumpPolicy extends BasePolicy
{
    /**
     * @param  Dump  $dump
     */
    public function view(?User $user, Model $dump): Response
    {
        if (Filament::isServing()) {
            return $user instanceof User && $user->can(CrudPermission::VIEW->format(static::getModel()))
                ? Response::allow()
                : Response::deny();
        }

        if ($user?->hasRole(Role::ADMIN->value)) {
            return Response::allow();
        }

        return Str::contains($dump->path, Dump::safeDumps())
            ? Response::allow()
            : Response::deny();
    }
}
