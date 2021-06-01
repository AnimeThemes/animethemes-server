<?php declare(strict_types=1);

namespace App\Contracts\Events;

/**
 * Interface UpdateRelatedIndicesEvent
 * @package App\Contracts\Events
 */
interface UpdateRelatedIndicesEvent
{
    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices();
}
