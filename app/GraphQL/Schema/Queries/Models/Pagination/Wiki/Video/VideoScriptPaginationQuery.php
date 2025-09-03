<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\Wiki\Video;

use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\Wiki\Video\VideoScriptType;

class VideoScriptPaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('videoscriptPagination');
    }

    public function description(): string
    {
        return 'Returns a listing of scripts resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): VideoScriptType
    {
        return new VideoScriptType();
    }
}
