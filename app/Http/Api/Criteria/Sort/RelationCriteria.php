<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Sort;

use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Class RelationCriteria.
 */
class RelationCriteria extends FieldCriteria
{
    /**
     * Apply criteria to builder.
     *
     * @param Builder $builder
     * @param string $column
     * @return Builder
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function applySort(Builder $builder, string $column): Builder
    {
        $includePath = Str::beforeLast($this->field, '.');
        $fieldName = Str::afterLast($this->field, '.');

        $relation = $builder->getRelation($includePath);

        $orderBySubQuery = $relation->getRelationExistenceQuery($relation->getQuery(), $builder, [$fieldName]);

        return $builder->orderBy($orderBySubQuery->toBase(), $this->direction->value);
    }

    /**
     * Apply criteria to builder.
     *
     * @param SearchRequestBuilder $builder
     * @param string $column
     * @return SearchRequestBuilder
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function applyElasticsearchSort(SearchRequestBuilder $builder, string $column): SearchRequestBuilder
    {
        return $builder->sortRaw([
            [
                $this->field => [
                    'order' => $this->direction->value,
                    'nested' => [
                        'path' => Str::beforeLast($this->field, '.'),
                    ]
                ],
            ]
        ]);
    }
}
