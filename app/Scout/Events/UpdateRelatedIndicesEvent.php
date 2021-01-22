<?php

namespace App\Scout\Events;

interface UpdateRelatedIndicesEvent
{
    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices();
}
