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
 * @extends BaseEvent<Performance>
 */
class PerformanceDeleting extends BaseEvent implements UpdateRelatedIndicesEvent
{
    public function updateRelatedIndices(): void
    {
        $performance = $this->getModel()->load([
            Performance::RELATION_ARTIST => function (MorphTo $morphTo): void {
                $morphTo->morphWith([
                    Artist::class => [],
                    Membership::class => [Membership::RELATION_GROUP, Membership::RELATION_MEMBER],
                ]);
            },
        ]);

        if ($performance->isForceDeleting()) {
            if ($performance->isMembership()) {
                $performance->artist->group->searchable();
                $performance->artist->member->searchable();

                return;
            }

            $performance->artist->searchable();
        }
    }
}
