<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Criteria\Sort;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\FieldCriteria as BaseCriteria;
use App\Http\Api\Sort\Sort;

/**
 * Class FieldCriteria.
 */
class FieldCriteria extends Criteria
{
    protected readonly Direction $direction;

    /**
     * Create a new criteria instance.
     *
     * @param  BaseCriteria  $criteria
     */
    public function __construct(BaseCriteria $criteria)
    {
        parent::__construct($criteria);
        $this->direction = $criteria->getDirection();
    }

    /**
     * Apply criteria to builder.
     *
     * @param  Sort  $sort
     * @return array
     */
    public function sort(Sort $sort): array
    {
        return [$sort->getColumn() => $this->direction->value];
    }
}
