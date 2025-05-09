<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Models\Wiki;

/**
 * Interface BackfillResources.
 */
interface BackfillResources
{
    /**
     * Get the available sites to backfill.
     *
     * @return array<int, string>
     */
    public function getResourcesMapping(): array;
}
