<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Singular\Wiki;

use App\GraphQL\Schema\Queries\Models\Singular\EloquentSingularQuery;
use App\GraphQL\Schema\Types\Wiki\VideoType;

class VideoQuery extends EloquentSingularQuery
{
    public function __construct()
    {
        parent::__construct('video');
    }

    public function description(): string
    {
        return 'Returns a video resource.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): VideoType
    {
        return new VideoType();
    }
}
