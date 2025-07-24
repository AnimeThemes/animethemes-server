<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Search;

readonly class Criteria
{
    public function __construct(protected string $term) {}

    /**
     * Get the search term.
     */
    public function getTerm(): string
    {
        return $this->term;
    }
}
