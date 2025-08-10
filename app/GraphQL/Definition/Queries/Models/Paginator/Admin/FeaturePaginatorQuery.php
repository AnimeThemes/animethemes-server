<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Admin;

use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Admin\FeatureType;

class FeaturePaginatorQuery extends EloquentPaginatorQuery
{
    public function __construct()
    {
        parent::__construct('featurePaginator');
    }

    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Returns a listing of feature resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): FeatureType
    {
        return new FeatureType();
    }
}
