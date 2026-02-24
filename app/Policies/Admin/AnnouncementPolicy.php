<?php

declare(strict_types=1);

namespace App\Policies\Admin;

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Announcement;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class AnnouncementPolicy extends BasePolicy
{
    /**
     * @param  Announcement  $announcement
     */
    public function view(?User $user, Model $announcement): Response
    {
        if (Filament::isServing()) {
            return $user?->can(CrudPermission::VIEW->format(static::getModel()))
                ? Response::allow()
                : Response::deny();
        }

        /** @phpstan-ignore-next-line */
        return parent::view($user, $announcement)
            && $announcement->start_at->isPast()
            && $announcement->end_at->isFuture()
            ? Response::allow()
            : Response::deny();
    }
}
