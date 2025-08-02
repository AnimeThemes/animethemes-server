<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Sort;

use App\Enums\GraphQL\SortDirection;
use Stringable;

class Sort implements Stringable
{
    public function __construct(
        protected string $name,
        protected SortDirection $direction = SortDirection::ASC,
    ) {}

    /**
     * Get the sort as an enum representation.
     */
    public function __toString(): string
    {
        return match ($this->direction) {
            SortDirection::ASC => SortDirection::resolveForAsc($this->name),
            SortDirection::DESC => SortDirection::resolveForDesc($this->name),
        };
    }
}
