<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Pagination\Wiki;

use App\GraphQL\Definition\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Definition\Types\Wiki\VideoType;

class VideoPaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('videoPagination');
    }

    public function description(): string
    {
        return 'Returns a listing of video resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): VideoType
    {
        return new VideoType();
    }
}
