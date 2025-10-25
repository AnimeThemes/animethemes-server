<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Performance;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @extends WikiRestoredEvent<Performance>
 */
class PerformanceRestored extends WikiRestoredEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(Performance $performance)
    {
        parent::__construct($performance);
    }

    public function getModel(): Performance
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Performance '**{$this->getModel()->getName()}**' has been restored.";
    }

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

        if ($performance->isMembership()) {
            $performance->artist->group->searchable();
            $performance->artist->member->searchable();

            return;
        }

        $performance->artist->searchable();
    }
}
