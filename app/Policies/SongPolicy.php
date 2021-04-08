<?php

namespace App\Policies;

use App\Models\Artist;
use App\Models\Song;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SongPolicy
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
     * @param  \App\Models\Song  $song
     * @return mixed
     */
    public function view(User $user, Song $song)
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
        return $user->hasCurrentTeamPermission('song:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Song  $song
     * @return mixed
     */
    public function update(User $user, Song $song)
    {
        return $user->hasCurrentTeamPermission('song:update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Song  $song
     * @return mixed
     */
    public function delete(User $user, Song $song)
    {
        return $user->hasCurrentTeamPermission('song:delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Song  $song
     * @return mixed
     */
    public function restore(User $user, Song $song)
    {
        return $user->hasCurrentTeamPermission('song:restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Song  $song
     * @return mixed
     */
    public function forceDelete(User $user, Song $song)
    {
        return $user->hasCurrentTeamPermission('song:forceDelete');
    }

    /**
     * Determine whether the user can add a theme to the song.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Song  $song
     * @return mixed
     */
    public function addTheme(User $user, Song $song)
    {
        return $user->hasCurrentTeamPermission('songtheme:create');
    }

    /**
     * Determine whether the user can attach any artist to the song.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Song  $song
     * @return mixed
     */
    public function attachAnyArtist(User $user, Song $song)
    {
        return $user->hasCurrentTeamPermission('song:update');
    }

    /**
     * Determine whether the user can attach an artist to the song.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Song  $song
     * @param  \App\Models\Artist  $artist
     * @return mixed
     */
    public function attachArtist(User $user, Song $song, Artist $artist)
    {
        return $user->hasCurrentTeamPermission('song:update');
    }

    /**
     * Determine whether the user can detach an artist from the song.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Song  $song
     * @param  \App\Models\Artist  $artist
     * @return mixed
     */
    public function detachArtist(User $user, Song $song, Artist $artist)
    {
        return $user->hasCurrentTeamPermission('song:update');
    }
}
