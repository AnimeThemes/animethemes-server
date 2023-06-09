<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;
use App\Pivots\Wiki\StudioImage;
use Illuminate\Auth\Access\HandlesAuthorization;
use Laravel\Nova\Nova;

/**
 * Class StudioPolicy.
 */
class StudioPolicy
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
        return Nova::whenServing(
            fn (): bool => $user !== null && $user->can(CrudPermission::VIEW->format(Studio::class)),
            fn (): bool => true
        );
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function view(?User $user): bool
    {
        return Nova::whenServing(
            fn (): bool => $user !== null && $user->can(CrudPermission::VIEW->format(Studio::class)),
            fn (): bool => true
        );
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Studio::class));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Studio  $studio
     * @return bool
     */
    public function update(User $user, Studio $studio): bool
    {
        return ! $studio->trashed() && $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Studio  $studio
     * @return bool
     */
    public function delete(User $user, Studio $studio): bool
    {
        return ! $studio->trashed() && $user->can(CrudPermission::DELETE->format(Studio::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Studio  $studio
     * @return bool
     */
    public function restore(User $user, Studio $studio): bool
    {
        return $studio->trashed() && $user->can(ExtendedCrudPermission::RESTORE->format(Studio::class));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(Studio::class));
    }

    /**
     * Determine whether the user can attach any anime to the studio.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyAnime(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can attach an anime to the studio.
     *
     * @param  User  $user
     * @param  Studio  $studio
     * @param  Anime  $anime
     * @return bool
     */
    public function attachAnime(User $user, Studio $studio, Anime $anime): bool
    {
        $attached = AnimeStudio::query()
            ->where($anime->getKeyName(), $anime->getKey())
            ->where($studio->getKeyName(), $studio->getKey())
            ->exists();

        return ! $attached && $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can detach an anime from the studio.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnime(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can attach any resource to the studio.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can attach a resource to the studio.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can detach a resource from the studio.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can attach any image to the studio.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyImage(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can attach an image to the studio.
     *
     * @param  User  $user
     * @param  Studio  $studio
     * @param  Image  $image
     * @return bool
     */
    public function attachImage(User $user, Studio $studio, Image $image): bool
    {
        $attached = StudioImage::query()
            ->where($studio->getKeyName(), $studio->getKey())
            ->where($image->getKeyName(), $image->getKey())
            ->exists();

        return ! $attached && $user->can(CrudPermission::UPDATE->format(Studio::class));
    }

    /**
     * Determine whether the user can detach an image from the studio.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachImage(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Studio::class));
    }
}
