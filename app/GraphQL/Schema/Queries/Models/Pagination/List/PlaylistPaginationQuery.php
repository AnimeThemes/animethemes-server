<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\List;

use App\Enums\Models\List\PlaylistVisibility;
use App\GraphQL\Argument\Argument;
use App\GraphQL\Argument\SearchArgument;
use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\List\PlaylistType;
use App\Models\List\Playlist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PlaylistPaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('playlistPagination');
    }

    public function description(): string
    {
        return 'Returns a listing of playlist resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): PlaylistType
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

    protected function query(Builder $builder, array $args): Builder
    {
        $builder->where(Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::PUBLIC->value);

        if ($user = Auth::user()) {
            return $builder->orWhereBelongsTo($user, Playlist::RELATION_USER);
        }

        return $builder;
    }
}
