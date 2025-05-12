<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\List;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\GraphQL\Mutations\List\PlaylistMutator;
use App\GraphQL\Policies\BasePolicy;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Support\Arr;

/**
 * Class PlaylistPolicy.
 */
class PlaylistPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  User|null  $user
     * @param  array|null  $injected
     * @return bool
     */
    public function viewAny(?User $user, ?array $injected = null): bool
    {
        return $user === null || $user->can(CrudPermission::VIEW->format(Playlist::class));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  array|null  $injected
     * @return bool
     */
    public function view(?User $user, ?array $injected = null): bool
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($injected, PlaylistMutator::ROUTE_SLUG);

        return $user !== null
            ? ($playlist->user()->is($user) || PlaylistVisibility::PRIVATE !== $playlist->visibility) && $user->can(CrudPermission::VIEW->format(Playlist::class))
            : PlaylistVisibility::PRIVATE !== $playlist->visibility;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @param  array|null  $injected
     * @return bool
     */
    public function create(User $user, ?array $injected = null): bool
    {
        return $user->can(CrudPermission::CREATE->format(Playlist::class));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  array  $injected
     * @return bool
     */
    public function update(User $user, array $injected): bool
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($injected, PlaylistMutator::ROUTE_SLUG);

        return !$playlist->trashed() && $playlist->user()->is($user) && $user->can(CrudPermission::UPDATE->format(Playlist::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  array  $injected
     * @return bool
     */
    public function delete(User $user, array $injected): bool
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($injected, PlaylistMutator::ROUTE_SLUG);

        return !$playlist->trashed() && $playlist->user()->is($user) && $user->can(CrudPermission::DELETE->format(Playlist::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  array  $injected
     * @return bool
     */
    public function restore(User $user, array $injected): bool
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($injected, PlaylistMutator::ROUTE_SLUG);

        return $playlist->trashed() && $playlist->user()->is($user) && $user->can(ExtendedCrudPermission::RESTORE->format(Playlist::class));
    }
}
