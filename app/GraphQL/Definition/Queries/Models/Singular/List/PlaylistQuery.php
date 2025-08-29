<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Singular\List;

use App\GraphQL\Definition\Queries\Models\Singular\EloquentSingularQuery;
use App\GraphQL\Definition\Types\List\PlaylistType;

class PlaylistQuery extends EloquentSingularQuery
{
    public function __construct()
    {
        parent::__construct('playlist');
    }

    public function description(): string
    {
        return 'Returns a playlist resource.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): PlaylistType
    {
        return new PlaylistType();
    }
}
