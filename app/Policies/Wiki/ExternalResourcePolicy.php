<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use App\Policies\BasePolicy;

/**
 * Class ExternalResourcePolicy.
 */
class ExternalResourcePolicy extends BasePolicy
{
    /**
     * Determine whether the user can attach any artist to the resource.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyArtist(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(ExternalResource::class));
    }

    /**
     * Determine whether the user can attach an artist to the resource.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachArtist(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(ExternalResource::class));
    }

    /**
     * Determine whether the user can detach an artist from the resource.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachArtist(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(ExternalResource::class));
    }

    /**
     * Determine whether the user can attach any anime to the resource.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyAnime(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(ExternalResource::class));
    }

    /**
     * Determine whether the user can attach an anime to the resource.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnime(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(ExternalResource::class));
    }

    /**
     * Determine whether the user can detach an anime from the resource.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnime(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(ExternalResource::class));
    }

    /**
     * Determine whether the user can attach any studio to the resource.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyStudio(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(ExternalResource::class));
    }

    /**
     * Determine whether the user can attach a studio to the resource.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachStudio(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(ExternalResource::class));
    }

    /**
     * Determine whether the user can detach a studio from the resource.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachStudio(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(ExternalResource::class));
    }
}
