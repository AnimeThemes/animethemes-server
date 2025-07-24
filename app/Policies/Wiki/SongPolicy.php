<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
use App\Pivots\Wiki\SongResource;
use App\Policies\BasePolicy;

class SongPolicy extends BasePolicy
{
    /**
     * Determine whether the user can attach any artist to the song.
     */
    public function attachAnyArtist(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Song::class)) && $user->can(CrudPermission::CREATE->format(Artist::class));
    }

    /**
     * Determine whether the user can attach an artist to the song.
     */
    public function attachArtist(User $user, Song $song, Artist $artist): bool
    {
        $attached = ArtistSong::query()
            ->where(ArtistSong::ATTRIBUTE_SONG, $song->getKey())
            ->where(ArtistSong::ATTRIBUTE_ARTIST, $artist->getKey())
            ->exists();

        return ! $attached
            && $user->can(CrudPermission::CREATE->format(Song::class))
            && $user->can(CrudPermission::CREATE->format(Artist::class));
    }

    /**
     * Determine whether the user can detach any artist from the song.
     */
    public function detachAnyArtist(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Song::class)) && $user->can(CrudPermission::DELETE->format(Artist::class));
    }

    /**
     * Determine whether the user can add a theme to the song.
     */
    public function addAnimeTheme(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(AnimeTheme::class));
    }

    /**
     * Determine whether the user can attach any resource to the song.
     */
    public function attachAnyExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Song::class)) && $user->can(CrudPermission::CREATE->format(ExternalResource::class));
    }

    /**
     * Determine whether the user can attach a resource to the song.
     */
    public function attachExternalResource(User $user, Song $song, ExternalResource $resource): bool
    {
        $attached = SongResource::query()
            ->where(SongResource::ATTRIBUTE_SONG, $song->getKey())
            ->where(SongResource::ATTRIBUTE_RESOURCE, $resource->getKey())
            ->exists();

        return ! $attached
            && $user->can(CrudPermission::CREATE->format(Song::class))
            && $user->can(CrudPermission::CREATE->format(ExternalResource::class));
    }

    /**
     * Determine whether the user can detach any resource from the song.
     */
    public function detachAnyExternalResource(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(Song::class)) && $user->can(CrudPermission::DELETE->format(ExternalResource::class));
    }
}
