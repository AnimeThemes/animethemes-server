<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Sort;

use App\Http\Api\Sort\Sort;
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
     * @param  Builder  $builder
     * @param  Sort  $sort
     * @return Builder
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function sort(Builder $builder, Sort $sort): Builder
    {
        $includePath = Str::beforeLast($this->field, '.');
        $fieldName = Str::afterLast($this->field, '.');

        $relation = $builder->getRelation($includePath);

        $orderBySubQuery = $relation->getRelationExistenceQuery($relation->getQuery(), $builder, [$fieldName]);

        return $builder->orderBy($orderBySubQuery->toBase(), $this->direction->value);
    }
}
