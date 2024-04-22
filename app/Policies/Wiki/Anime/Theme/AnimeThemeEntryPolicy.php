<?php

declare(strict_types=1);

namespace App\Policies\Wiki\Anime\Theme;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Auth\Access\HandlesAuthorization;
use Laravel\Nova\Nova;

/**
 * Class AnimeThemeEntryPolicy.
 */
class AnimeThemeEntryPolicy
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
            fn (): bool => $user !== null && $user->can(CrudPermission::VIEW->format(AnimeThemeEntry::class)),
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
            fn (): bool => $user !== null && $user->can(CrudPermission::VIEW->format(AnimeThemeEntry::class)),
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
        return $user->can(CrudPermission::CREATE->format(AnimeThemeEntry::class));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  AnimeThemeEntry  $animethemeentry
     * @return bool
     */
    public function update(User $user, AnimeThemeEntry $animethemeentry): bool
    {
        return ! $animethemeentry->trashed() && $user->can(CrudPermission::UPDATE->format(AnimeThemeEntry::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  AnimeThemeEntry  $animethemeentry
     * @return bool
     */
    public function delete(User $user, AnimeThemeEntry $animethemeentry): bool
    {
        return ! $animethemeentry->trashed() && $user->can(CrudPermission::DELETE->format(AnimeThemeEntry::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  AnimeThemeEntry  $animethemeentry
     * @return bool
     */
    public function restore(User $user, AnimeThemeEntry $animethemeentry): bool
    {
        return $animethemeentry->trashed() && $user->can(ExtendedCrudPermission::RESTORE->format(AnimeThemeEntry::class));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(AnimeThemeEntry::class));
    }

    /**
     * Determine whether the user can permanently delete any model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(AnimeThemeEntry::class));
    }

    /**
     * Determine whether the user can attach any video to the entry.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyVideo(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(AnimeThemeEntry::class));
    }

    /**
     * Determine whether the user can attach a video to the entry.
     *
     * @param  User  $user
     * @param  AnimeThemeEntry  $entry
     * @param  Video  $video
     * @return bool
     */
    public function attachVideo(User $user, AnimeThemeEntry $entry, Video $video): bool
    {
        $attached = AnimeThemeEntryVideo::query()
            ->where($entry->getKeyName(), $entry->getKey())
            ->where($video->getKeyName(), $video->getKey())
            ->exists();

        return ! $attached && $user->can(CrudPermission::UPDATE->format(AnimeThemeEntry::class));
    }

    /**
     * Determine whether the user can detach a video from the entry.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachVideo(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(AnimeThemeEntry::class));
    }
}
