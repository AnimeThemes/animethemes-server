<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\List;

use App\Enums\Auth\CrudPermission;
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
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  array|null  $injected
     * @param  string|null  $keyName
     * @return bool
     */
    public function view(?User $user, ?array $injected = null, ?string $keyName = PlaylistMutator::ROUTE_SLUG): bool
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($injected, $keyName);

        if ($user !== null) {
            return ($playlist->user()->is($user) || $playlist->visibility !== PlaylistVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(Playlist::class));
        }

        return $playlist->visibility !== PlaylistVisibility::PRIVATE;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  array  $injected
     * @param  string|null  $keyName
     * @return bool
     */
    public function update(User $user, array $injected, ?string $keyName = PlaylistMutator::ROUTE_SLUG): bool
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($injected, $keyName);

        return $playlist->user()->is($user) && parent::update($user, $injected, $keyName);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  array  $injected
     * @param  string|null  $keyName
     * @return bool
     */
    public function delete(User $user, array $injected, ?string $keyName = PlaylistMutator::ROUTE_SLUG): bool
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($injected, $keyName);

        return $playlist->user()->is($user) && parent::delete($user, $injected, $keyName);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  array  $injected
     * @param  string|null  $keyName
     * @return bool
     */
    public function restore(User $user, array $injected, ?string $keyName = PlaylistMutator::ROUTE_SLUG): bool
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($injected, $keyName);

        return $playlist->user()->is($user) && parent::restore($user, $injected, $keyName);
    }
}
