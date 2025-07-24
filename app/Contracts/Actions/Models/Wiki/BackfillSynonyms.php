<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Models\Wiki;

interface BackfillSynonyms
{
    /**
     * Get the mapping for the synonyms.
     *
     * @return string[]
     */
    public function getSynonymsMapping(): array;
}
