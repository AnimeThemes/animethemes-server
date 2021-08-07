<?php

declare(strict_types=1);

namespace App\Http\Api\Sort;

use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Criteria\Sort\RandomCriteria;
use Illuminate\Support\Collection;

/**
 * Class RandomSort.
 */
class RandomSort extends Sort
{
    /**
     * Create a new sort instance.
     *
     * @param Collection<Criteria> $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, RandomCriteria::PARAM_VALUE);
    }

    /**
     * Determine if this sort should be applied.
     *
     * @return bool
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function shouldApplySort(): bool
    {
        return $this->criteria->count() === 1;
    }
}
