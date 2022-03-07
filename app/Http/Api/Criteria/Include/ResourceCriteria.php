<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Include;

use Illuminate\Support\Collection;

/**
 * Class ResourceCriteria.
 */
class ResourceCriteria extends Criteria
{
    /**
     * Create a new criteria instance.
     *
     * @param  string  $type
     * @param  Collection  $paths
     */
    public function __construct(protected readonly string $type, Collection $paths)
    {
        parent::__construct($paths);
    }

    /**
     * Get the resource type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
