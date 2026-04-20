<?php

declare(strict_types=1);

namespace App\GraphQL\Builders\List;

use App\Enums\Models\List\PlaylistVisibility;
use App\Models\List\Playlist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PlaylistPaginationBuilder
{
    /**
     * @param  array<string, mixed>  $args
     */
    public function __invoke(Builder $builder, null $_, null $root, $args): Builder
    {
        $builder->where(Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::PUBLIC->value);

        if ($user = Auth::user()) {
            return $builder->orWhereBelongsTo($user, Playlist::RELATION_USER);
        }

        return $builder;
    }
}
