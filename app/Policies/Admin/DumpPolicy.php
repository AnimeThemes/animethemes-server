<?php

declare(strict_types=1);

namespace App\Policies\Admin;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DumpPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view the model.
     *
     * @param  Dump  $dump
     */
    public function view(?User $user, Model $dump): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->can(CrudPermission::VIEW->format(static::getModel()));
        }

        if ($user?->hasRole(Role::ADMIN->value)) {
            return true;
        }

        return Str::contains($dump->path, Dump::safeDumps());
    }
}
