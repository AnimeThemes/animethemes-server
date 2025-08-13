<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator\Admin;

use App\Constants\FeatureConstants;
use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\Admin\FeatureType;
use App\Models\Admin\Feature;
use Illuminate\Database\Eloquent\Builder;

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

    /**
     * Manage the query.
     */
    protected function query(Builder $builder, array $args): Builder
    {
        return $builder->where(Feature::ATTRIBUTE_SCOPE, FeatureConstants::NULL_SCOPE);
    }
}
