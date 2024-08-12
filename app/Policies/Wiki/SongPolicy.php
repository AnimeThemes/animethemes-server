<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Song;
use App\Policies\BasePolicy;

/**
 * Class SongPolicy.
 */
class SongPolicy extends BasePolicy
{
    /**
     * Determine whether the user can add a theme to the song.
     *
     * @param  User  $user
     * @return bool
     */
    public function addAnimeTheme(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Song::class));
    }

    /**
     * Determine whether the user can attach any artist to the song.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyArtist(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Song::class));
    }

    /**
     * Determine whether the user can attach an artist to the song.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachArtist(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Song::class));
    }

    /**
     * Determine whether the user can detach an artist from the song.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachArtist(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Song::class));
    }

    /**
     * Determine whether the user can attach any resource to the song.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Song::class));
    }

    /**
     * Determine whether the user can attach a resource to the song.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Song::class));
    }

    /**
     * Determine whether the user can detach a resource from the song.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Song::class));
    }
}
