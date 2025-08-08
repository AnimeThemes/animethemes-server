<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Wiki\Video;

use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Wiki\Video\VideoScriptType;

#[UsePaginateDirective]
class VideoScriptPaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('videoscriptPaginator');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of scripts resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): VideoScriptType
    {
        return new VideoScriptType();
    }
}
