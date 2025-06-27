<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeResource;
use App\Pivots\Wiki\ArtistResource;
use App\Pivots\Wiki\SongResource;
use App\Pivots\Wiki\StudioResource;
use App\Policies\BasePolicy;

/**
 * Class ExternalResourcePolicy.
 */
class ExternalResourcePolicy extends BasePolicy
{
    /**
     * Determine whether the user can attach any anime to the resource.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyAnime(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(ExternalResource::class)) && $user->can(CrudPermission::CREATE->format(Anime::class));
    }

    /**
     * Determine whether the user can attach an anime to the resource.
     *
     * @param  User  $user
     * @param  ExternalResource  $resource
     * @param  Anime  $anime
     * @return bool
     */
    public function attachAnime(User $user, ExternalResource $resource, Anime $anime): bool
    {
        $attached = AnimeResource::query()
            ->where(AnimeResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->where(AnimeResource::ATTRIBUTE_ANIME, $anime->getKey())
            ->exists();

        return ! $attached
            && $user->can(CrudPermission::CREATE->format(ExternalResource::class))
            && $user->can(CrudPermission::CREATE->format(Anime::class));
    }

    /**
     * Determine whether the user can detach any anime from the resource.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnyAnime(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(ExternalResource::class)) && $user->can(CrudPermission::DELETE->format(Anime::class));
    }

    /**
     * Determine whether the user can attach any artist to the resource.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyArtist(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(ExternalResource::class)) && $user->can(CrudPermission::CREATE->format(Artist::class));
    }

    /**
     * Determine whether the user can attach an artist to the resource.
     *
     * @param  User  $user
     * @param  ExternalResource  $resource
     * @param  Artist  $artist
     * @return bool
     */
    public function attachArtist(User $user, ExternalResource $resource, Artist $artist): bool
    {
        $attached = ArtistResource::query()
            ->where(ArtistResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->where(ArtistResource::ATTRIBUTE_ARTIST, $artist->getKey())
            ->exists();

        return ! $attached
        && $user->can(CrudPermission::CREATE->format(ExternalResource::class))
        && $user->can(CrudPermission::CREATE->format(Artist::class));
    }

    /**
     * Determine whether the user can detach any artist from the resource.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnyArtist(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(ExternalResource::class)) && $user->can(CrudPermission::DELETE->format(Artist::class));
    }

    /**
     * Determine whether the user can attach any song to the resource.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnySong(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(ExternalResource::class)) && $user->can(CrudPermission::CREATE->format(Song::class));
    }

    /**
     * Determine whether the user can attach a song to the resource.
     *
     * @param  User  $user
     * @param  ExternalResource  $resource
     * @param  Song  $song
     * @return bool
     */
    public function attachSong(User $user, ExternalResource $resource, Song $song): bool
    {
        $attached = SongResource::query()
            ->where(SongResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->where(SongResource::ATTRIBUTE_SONG, $song->getKey())
            ->exists();

        return ! $attached
            && $user->can(CrudPermission::CREATE->format(ExternalResource::class))
            && $user->can(CrudPermission::CREATE->format(Song::class));
    }

    /**
     * Determine whether the user can detach any song from the resource.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnySong(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(ExternalResource::class)) && $user->can(CrudPermission::DELETE->format(Song::class));
    }

    /**
     * Determine whether the user can attach any studio to the resource.
     *
     * @param  User  $user
     * @return bool
     */
    public function attachAnyStudio(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(ExternalResource::class)) && $user->can(CrudPermission::CREATE->format(Studio::class));
    }

    /**
     * Determine whether the user can attach a studio to the resource.
     *
     * @param  User  $user
     * @param  ExternalResource  $resource
     * @param  Studio  $studio
     * @return bool
     */
    public function attachStudio(User $user, ExternalResource $resource, Studio $studio): bool
    {
        $attached = StudioResource::query()
            ->where(StudioResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->where(StudioResource::ATTRIBUTE_STUDIO, $studio->getKey())
            ->exists();

        return ! $attached
            && $user->can(CrudPermission::CREATE->format(ExternalResource::class))
            && $user->can(CrudPermission::CREATE->format(Studio::class));
    }

    /**
     * Determine whether the user can detach any studio from the resource.
     *
     * @param  User  $user
     * @return bool
     */
    public function detachAnyStudio(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(ExternalResource::class)) && $user->can(CrudPermission::DELETE->format(Studio::class));
    }
}
