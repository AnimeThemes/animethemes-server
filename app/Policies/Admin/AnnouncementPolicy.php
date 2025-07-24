<?php

declare(strict_types=1);

namespace App\Policies\Admin;

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Announcement;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class AnnouncementPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view the model.
     *
     * @param  Announcement  $announcement
     */
    public function view(?User $user, Model $announcement): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->can(CrudPermission::VIEW->format(static::getModel()));
        }

        return $announcement->public;
    }
}
