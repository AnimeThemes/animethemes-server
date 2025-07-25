<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Attributes\UseSearchDirective;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\VideoType;

#[UsePaginateDirective]
#[UseSearchDirective]
class VideosQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('videos');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of video resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): VideoType
    {
        return new VideoType();
    }
}
