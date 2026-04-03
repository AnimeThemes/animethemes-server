<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Performance;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Song\Performance;

/**
 * @extends WikiRestoredEvent<Performance>
 */
class PerformanceRestored extends WikiRestoredEvent implements UpdateRelatedIndicesEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Performance '**{$this->getModel()->getName()}**' has been restored.";
    }

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
