<?php

declare(strict_types=1);

namespace App\Policies\Discord;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Discord\DiscordThread;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class DiscordThreadPolicy.
 */
class DiscordThreadPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        return Filament::isServing()
            ? $user !== null && $user->can(CrudPermission::VIEW->format(DiscordThread::class))
            : true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function view(?User $user): bool
    {
        return Filament::isServing()
            ? $user !== null && $user->can(CrudPermission::VIEW->format(DiscordThread::class))
            : true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(DiscordThread::class));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  DiscordThread  $discordThread
     * @return bool
     */
    public function update(User $user, DiscordThread $discordThread): bool
    {
        return ! $discordThread->trashed() && $user->can(CrudPermission::UPDATE->format(DiscordThread::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  DiscordThread  $discordThread
     * @return bool
     */
    public function delete(User $user, DiscordThread $discordThread): bool
    {
        return ! $discordThread->trashed() && $user->can(CrudPermission::DELETE->format(DiscordThread::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  DiscordThread  $discordThread
     * @return bool
     */
    public function restore(User $user, DiscordThread $discordThread): bool
    {
        return $discordThread->trashed() && $user->can(ExtendedCrudPermission::RESTORE->format(DiscordThread::class));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(DiscordThread::class));
    }

    /**
     * Determine whether the user can permanently delete any model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(DiscordThread::class));
    }
}
