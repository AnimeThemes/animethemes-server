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
     * The include paths.
     *
     * @var Collection
     */
    protected Collection $paths;

    /**
     * Create a new criteria instance.
     *
     * @param Collection $paths
     */
    public function __construct(Collection $paths)
    {
        $this->paths = $paths;
    }

    /**
     * Get the include paths.
     *
     * @return Collection
     */
    public function getPaths(): Collection
    {
        return $this->paths;
    }

    /**
     * Get the allowed include paths.
     *
     * @param array $allowedIncludePaths
     * @return Collection
     */
    public function getAllowedPaths(array $allowedIncludePaths): Collection
    {
        return collect($this->paths)->intersect($allowedIncludePaths);
    }
}
