<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Include;

use Illuminate\Support\Collection;

class Criteria
{
    /**
     * Create a new criteria instance.
     *
     * @param  Collection<int, string>  $paths
     */
    public function __construct(protected readonly Collection $paths) {}

    /**
     * Get include paths.
     *
     * @return Collection<int, string>
     */
    public function getPaths(): Collection
    {
        return $this->paths;
    }
}
