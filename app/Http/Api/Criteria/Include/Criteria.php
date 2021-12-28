<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Include;

use Illuminate\Support\Collection;

/**
 * Class Criteria.
 */
class Criteria
{
    /**
     * Create a new criteria instance.
     *
     * @param  Collection<string>  $paths
     */
    public function __construct(protected Collection $paths) {}

    /**
     * Get the include paths.
     *
     * @return Collection<string>
     */
    public function getPaths(): Collection
    {
        return $this->paths;
    }
}
