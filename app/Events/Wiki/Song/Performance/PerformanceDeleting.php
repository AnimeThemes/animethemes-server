<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Performance;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Song\Performance;

/**
 * Class PerformanceDeleting.
 *
 * @extends BaseEvent<Performance>
 */
class PerformanceDeleting extends BaseEvent implements UpdateRelatedIndicesEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Performance  $performance
     */
    public function __construct(Performance $performance)
    {
        parent::__construct($performance);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Performance
     */
    public function getModel(): Performance
    {
        return $this->model;
    }

    /**
     * Perform cascading deletes.
     *
     * @return void
     */
    public function updateRelatedIndices(): void
    {
        $performance = $this->getModel()->load([Performance::RELATION_ARTIST]);

        if ($performance->isForceDeleting()) {
            if ($performance->isMembership()) {
                $performance->artist->artist->searchable();
                $performance->artist->member->searchable();
            }

            $performance->artist->searchable();
        }
    }
}
