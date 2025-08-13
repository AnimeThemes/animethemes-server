<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\List;

use App\Enums\Models\List\PlaylistVisibility;
use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Argument\SearchArgument;
use App\Models\List\Playlist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PlaylistPaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('playlistPaginator');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of playlist resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): PlaylistType
    {
        return new PlaylistType();
    }

    /**
     * The arguments of the class resolve as customs class helper.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        return [
            ...parent::arguments(),

            new SearchArgument(),
        ];
    }

    /**
     * Manage the query.
     */
    protected function query(Builder $builder, array $args): Builder
    {
        $builder->where(Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::PUBLIC->value);

        if ($user = Auth::user()) {
            return $builder->orWhereBelongsTo($user, Playlist::RELATION_USER);
        }

        return $builder;
    }
}
