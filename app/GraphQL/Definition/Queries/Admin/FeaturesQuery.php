<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Admin;

use App\GraphQL\Attributes\UseBuilderDirective;
use App\GraphQL\Builders\Admin\FeatureBuilder;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Admin\FeatureType;

#[UseBuilderDirective(FeatureBuilder::class)]
class FeaturesQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('features');
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
