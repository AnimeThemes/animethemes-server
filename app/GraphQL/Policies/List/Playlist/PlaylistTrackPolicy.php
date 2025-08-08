<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\List\Playlist;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\GraphQL\Controllers\List\Playlist\PlaylistTrackController;
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
     * @param  array|null  $injected
     */
    public function viewAny(?User $user, ?array $injected = null): bool
    {
        /** @var Playlist|null $playlist */
        $playlist = Arr::get($injected, PlaylistTrackPlaylistField::FIELD);

        if ($user !== null) {
            return ($playlist?->user()->is($user) || $playlist?->visibility !== PlaylistVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(PlaylistTrack::class));
        }

        return $playlist?->visibility !== PlaylistVisibility::PRIVATE;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  array|null  $injected
     */
    public function view(?User $user, ?array $injected = null, ?string $keyName = 'id'): bool
    {
        /** @var Playlist|null $playlist */
        $playlist = Arr::get($injected, PlaylistTrackPlaylistField::FIELD);

        if ($user !== null) {
            return ($playlist?->user()->is($user) || $playlist?->visibility !== PlaylistVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(PlaylistTrack::class));
        }

        return $playlist?->visibility !== PlaylistVisibility::PRIVATE;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  array|null  $injected
     */
    public function create(User $user, ?array $injected = null): bool
    {
        /** @var Playlist|null $playlist */
        $playlist = Arr::get($injected, 'playlist');

        return $playlist?->user()->is($user) && parent::create($user, $injected);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  array  $injected
     */
    public function update(User $user, array $injected, ?string $keyName = 'id'): bool
    {
        /** @var Playlist|null $playlist */
        $playlist = Arr::get($injected, 'playlist');
        /** @var PlaylistTrack $track */
        $track = Arr::get($injected, PlaylistTrackController::ROUTE_SLUG);

        return $playlist?->user()->is($user) && parent::update($user, $injected, $keyName);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  array  $injected
     */
    public function delete(User $user, array $injected, ?string $keyName = 'id'): bool
    {
        /** @var Playlist|null $playlist */
        $playlist = Arr::get($injected, 'playlist');
        /** @var PlaylistTrack $track */
        $track = Arr::get($injected, PlaylistTrackController::ROUTE_SLUG);

        return $playlist?->user()->is($user) && parent::delete($user, $injected, $keyName);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  array  $injected
     */
    public function restore(User $user, array $injected, ?string $keyName = 'id'): bool
    {
        /** @var Playlist|null $playlist */
        $playlist = Arr::get($injected, 'playlist');
        /** @var PlaylistTrack $track */
        $track = Arr::get($injected, PlaylistTrackController::ROUTE_SLUG);

        return $playlist?->user()->is($user) && parent::restore($user, $injected, $keyName);
    }
}
