<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki\Video;

use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\Video\VideoScriptType;

#[UsePaginateDirective]
class VideoScriptsQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('scripts');
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
