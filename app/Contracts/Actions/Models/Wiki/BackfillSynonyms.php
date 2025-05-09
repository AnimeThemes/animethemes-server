<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Models\Wiki;

/**
 * Interface BackfillSynonyms.
 */
interface BackfillSynonyms
{
    /**
     * Get the mapping for the synonyms.
     *
     * @return array<int, string>
     */
    public function getSynonymsMapping(): array;
}
