<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Performance;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
        $performance = $this->getModel()->load([
            Performance::RELATION_ARTIST => function (MorphTo $morphTo) {
                $morphTo->morphWith([
                    Artist::class => [],
                    Membership::class => [Membership::RELATION_ARTIST, Membership::RELATION_MEMBER]
                ]);
            }
        ]);

        if ($performance->isForceDeleting()) {
            if ($performance->isMembership()) {
                $performance->artist->artist->searchable();
                $performance->artist->member->searchable();
            }

            $performance->artist->searchable();
        }
    }
}
