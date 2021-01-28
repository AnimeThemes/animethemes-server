<?php

namespace App\Contracts\Events;

interface UpdateRelatedIndicesEvent
{
    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices();
}
