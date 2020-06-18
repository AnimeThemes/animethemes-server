<?php

namespace App\Policies;

use App\Models\Artist;
use App\Models\ExternalResource;
use App\Models\Song;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArtistPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Artist  $artist
     * @return mixed
     */
    public function view(User $user, Artist $artist)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Artist  $artist
     * @return mixed
     */
    public function update(User $user, Artist $artist)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Artist  $artist
     * @return mixed
     */
    public function delete(User $user, Artist $artist)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Artist  $artist
     * @return mixed
     */
    public function restore(User $user, Artist $artist)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Artist  $artist
     * @return mixed
     */
    public function forceDelete(User $user, Artist $artist)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can attach any resource to the artist.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Artist  $artist
     * @return mixed
     */
    public function attachAnyExternalResource(User $user, Artist $artist)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can attach a resource to the artist.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Artist  $artist
     * @param  \App\Models\ExternalResource  $externalResource
     * @return mixed
     */
    public function attachExternalResource(User $user, Artist $artist, ExternalResource $externalResource)
    {
        if ($artist->externalResources->contains($externalResource)) {
            return false;
        }
        return $this->attachAnyExternalResource($user, $artist);
    }

    /**
     * Determine whether the user can detach a resource from the artist.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Artist  $artist
     * @param  \App\Models\ExternalResource  $externalResource
     * @return mixed
     */
    public function detachExternalResource(User $user, Artist $artist, ExternalResource $externalResource)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can attach any song to the artist.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Artist  $artist
     * @return mixed
     */
    public function attachAnySong(User $user, Artist $artist)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can attach a song to the artist.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Artist  $artist
     * @param  \App\Models\Song  $song
     * @return mixed
     */
    public function attachSong(User $user, Artist $artist, Song $song)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can detach a song from the artist.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Artist  $artist
     * @param  \App\Models\Song  $song
     * @return mixed
     */
    public function detachSong(User $user, Artist $artist, Song $song)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can attach any group/member to the artist.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Artist  $artist
     * @return mixed
     */
    public function attachAnyArtist(User $user, Artist $artist)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can attach a group/member to the artist.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Artist  $artist
     * @param  \App\Models\Artist  $member
     * @return mixed
     */
    public function attachArtist(User $user, Artist $artist, Artist $member)
    {
        return $user->isContributor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can detach a group/member from the artist.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Artist  $artist
     * @param  \App\Models\Artist  $member
     * @return mixed
     */
    public function detachArtist(User $user, Artist $artist, Artist $member)
    {
        return $user->isContributor() || $user->isAdmin();
    }
}
