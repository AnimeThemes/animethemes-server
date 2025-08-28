<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\List\Playlist;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\GraphQL\Definition\Fields\List\Playlist\PlaylistTrack\PlaylistTrackPlaylistField;
use App\GraphQL\Policies\BasePolicy;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Support\Arr;

class PlaylistTrackPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  array  $args
     */
    public function viewAny(?User $user, array $args = []): bool
    {
        /** @var Playlist|null $playlist */
        $playlist = Arr::get($args, PlaylistTrackPlaylistField::FIELD);

        if ($user !== null) {
            return ($playlist?->user()->is($user) || $playlist?->visibility !== PlaylistVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(PlaylistTrack::class));
        }

        return $playlist?->visibility !== PlaylistVisibility::PRIVATE;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  array  $args
     */
    public function view(?User $user, array $args = [], ?string $keyName = 'model'): bool
    {
        /** @var Playlist|null $playlist */
        $playlist = Arr::get($args, PlaylistTrackPlaylistField::FIELD);

        if ($user !== null) {
            return ($playlist?->user()->is($user) || $playlist?->visibility !== PlaylistVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(PlaylistTrack::class));
        }

        return $playlist?->visibility !== PlaylistVisibility::PRIVATE;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  array  $args
     */
    public function create(User $user, array $args = []): bool
    {
        /** @var Playlist|null $playlist */
        $playlist = Arr::get($args, PlaylistTrackPlaylistField::FIELD);

        return $playlist?->user()->is($user) && parent::create($user, $args);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  array  $args
     */
    public function update(User $user, array $args, ?string $keyName = 'model'): bool
    {
        /** @var Playlist|null $playlist */
        $playlist = Arr::get($args, PlaylistTrackPlaylistField::FIELD);
        /** @var PlaylistTrack $track */
        $track = Arr::get($args, $keyName);

        return $playlist?->user()->is($user) && parent::update($user, $args, $keyName);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  array  $args
     */
    public function delete(User $user, array $args, ?string $keyName = 'model'): bool
    {
        /** @var Playlist|null $playlist */
        $playlist = Arr::get($args, PlaylistTrackPlaylistField::FIELD);
        /** @var PlaylistTrack $track */
        $track = Arr::get($args, $keyName);

        return $playlist?->user()->is($user) && parent::delete($user, $args, $keyName);
    }
}
