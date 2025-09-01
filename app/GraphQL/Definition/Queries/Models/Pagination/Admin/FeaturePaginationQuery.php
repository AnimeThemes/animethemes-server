<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Pagination\Admin;

use App\Constants\FeatureConstants;
use App\GraphQL\Definition\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Definition\Types\Admin\FeatureType;
use App\Models\Admin\Feature;
use Illuminate\Database\Eloquent\Builder;

class FeaturePaginationQuery extends EloquentPaginationQuery
{
    public function __construct()
    {
        parent::__construct('featurePagination');
    }

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
