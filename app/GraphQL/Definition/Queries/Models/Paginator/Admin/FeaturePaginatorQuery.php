<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Admin;

use App\GraphQL\Attributes\Resolvers\UseBuilderDirective;
use App\GraphQL\Attributes\Resolvers\UsePaginateDirective;
use App\GraphQL\Controllers\Admin\FeatureController;
use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Admin\FeatureType;

#[UseBuilderDirective(FeatureController::class)]
#[UsePaginateDirective]
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
    public function baseType(): FeatureType
    {
        return new FeatureType();
    }
}
