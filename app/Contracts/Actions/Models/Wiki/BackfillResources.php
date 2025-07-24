<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Models\Wiki;

interface BackfillResources
{
    /**
     * Get the available sites to backfill.
     *
     * @return string[]
     */
    public function getResourcesMapping(): array;
}
