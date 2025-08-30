<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\List;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\GraphQL\Policies\BasePolicy;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Support\Arr;

class PlaylistPolicy extends BasePolicy
{
    /**
     * @param  array  $args
     */
    public function view(?User $user, array $args = [], ?string $keyName = 'model'): bool
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($args, $keyName);

        if ($user !== null) {
            return ($playlist->user()->is($user) || $playlist->visibility !== PlaylistVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(Playlist::class));
        }

        return $playlist->visibility !== PlaylistVisibility::PRIVATE;
    }

    /**
     * @param  array  $args
     */
    public function update(User $user, array $args, ?string $keyName = 'model'): bool
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($args, $keyName);

        return $playlist->user()->is($user) && parent::update($user, $args, $keyName);
    }

    /**
     * @param  array  $args
     */
    public function delete(User $user, array $args, ?string $keyName = 'model'): bool
    {
        /** @var Playlist $playlist */
        $playlist = Arr::get($args, $keyName);

        return $playlist->user()->is($user) && parent::delete($user, $args, $keyName);
    }
}
