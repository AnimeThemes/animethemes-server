<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Search;

/**
 * Class Criteria.
 */
class Criteria
{
    /**
     * The search term.
     *
     * @var string
     */
    protected string $term;

    /**
     * Create a new criteria instance.
     *
     * @param string $term
     */
    public function __construct(string $term)
    {
        $this->term = $term;
    }

    /**
     * Get the search term.
     *
     * @return string
     */
    public function getTerm(): string
    {
        return $this->term;
    }
}
