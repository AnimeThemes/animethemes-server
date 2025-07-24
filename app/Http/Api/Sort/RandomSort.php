<?php

declare(strict_types=1);

namespace App\Http\Api\Sort;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\RandomCriteria;

class RandomSort extends Sort
{
    /**
     * Create a new sort instance.
     */
    public function __construct()
    {
        parent::__construct(RandomCriteria::PARAM_VALUE, RandomCriteria::PARAM_VALUE);
    }

    /**
     * Format the sort based on direction.
     *
     * @param  Direction  $direction
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function format(Direction $direction): string
    {
        return $this->getKey();
    }
}
