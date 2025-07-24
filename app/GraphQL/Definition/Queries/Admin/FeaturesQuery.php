<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Admin;

use App\GraphQL\Attributes\UseBuilder;
use App\GraphQL\Builders\Admin\FeatureBuilder;
use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Admin\FeatureType;

#[UseBuilder(FeatureBuilder::class)]
class FeaturesQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('features');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of feature resources given fields.';
    }

    /**
     * The arguments of the type.
     *
     * @return string[]
     */
    public function arguments(): array
    {
        return [
            ...parent::arguments(),

            'orderBy: _ @orderBy(columnsEnum: "FeatureColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return FeatureType
     */
    public function baseType(): FeatureType
    {
        return new FeatureType();
    }
}
