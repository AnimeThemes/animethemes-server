<?php

declare(strict_types=1);

namespace App\Policies\Wiki\Video;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Video\VideoScript;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class VideoScriptPolicy.
 */
class VideoScriptPolicy
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
        if (Filament::isServing()) {
            return $user !== null && $user->can(CrudPermission::VIEW->format(VideoScript::class));
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function view(?User $user): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->can(CrudPermission::VIEW->format(VideoScript::class));
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(VideoScript::class));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  VideoScript  $videoscript
     * @return bool
     */
    public function update(User $user, VideoScript $videoscript): bool
    {
        return !$videoscript->trashed() && $user->can(CrudPermission::UPDATE->format(VideoScript::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  VideoScript  $videoscript
     * @return bool
     */
    public function delete(User $user, VideoScript $videoscript): bool
    {
        return !$videoscript->trashed() && $user->can(CrudPermission::DELETE->format(VideoScript::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  VideoScript  $videoscript
     * @return bool
     */
    public function restore(User $user, VideoScript $videoscript): bool
    {
        return $videoscript->trashed() && $user->can(ExtendedCrudPermission::RESTORE->format(VideoScript::class));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(VideoScript::class));
    }

    /**
     * Determine whether the user can permanently delete any model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(VideoScript::class));
    }
}
