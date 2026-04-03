<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Performance;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Song\Performance;

/**
 * @extends BaseEvent<Performance>
 */
class PerformanceDeleting extends BaseEvent implements UpdateRelatedIndicesEvent
{
    public function updateRelatedIndices(): void
    {
        $performance = $this->getModel()->load([
            Performance::RELATION_ARTIST,
            Performance::RELATION_MEMBER,
        ]);

        $performance->artist->searchable();
        $performance->member?->searchable();
    }
}
