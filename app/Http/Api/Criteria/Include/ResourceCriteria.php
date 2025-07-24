<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Include;

use Illuminate\Support\Collection;

class ResourceCriteria extends Criteria
{
    /**
     * @param  string  $type
     * @param  Collection<int, string>  $paths
     */
    public function __construct(protected readonly string $type, Collection $paths)
    {
        parent::__construct($paths);
    }

    /**
     * Get the resource type.
     */
    public function getType(): string
    {
        return $this->type;
    }
}
