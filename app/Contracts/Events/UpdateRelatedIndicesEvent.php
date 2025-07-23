<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface UpdateRelatedIndicesEvent
{
    /**
     * Perform updates on related indices.
     */
    public function updateRelatedIndices(): void;
}
