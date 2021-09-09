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
     * The type that these paths belong to.
     *
     * @var string
     */
    protected string $type;

    /**
     * Create a new criteria instance.
     *
     * @param  string  $type
     * @param  Collection  $paths
     */
    public function __construct(string $type, Collection $paths)
    {
        parent::__construct($paths);

        $this->type = $type;
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
