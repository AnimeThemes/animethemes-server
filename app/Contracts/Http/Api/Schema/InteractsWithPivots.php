<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Schema;

use App\Http\Api\Include\AllowedInclude;

/**
 * Interface InteractsWithPivots.
 */
interface InteractsWithPivots
{
    /**
     * Get the allowed pivots of the schema.
     *
     * @return AllowedInclude[]
     */
    public function allowedPivots(): array;
}
