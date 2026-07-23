<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Models\Wiki;

interface BackfillAlternativeTitles
{
    /**
     * Get the mapping for the alternative titles.
     *
     * @return array<int|string, string>
     */
    public function getAlternativeTitlesMapping(): array;
}
