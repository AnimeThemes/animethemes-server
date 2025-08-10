<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Wiki;

use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Wiki\AudioType;

class AudioPaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('audioPaginator');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of audio resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): AudioType
    {
        return new AudioType();
    }
}
