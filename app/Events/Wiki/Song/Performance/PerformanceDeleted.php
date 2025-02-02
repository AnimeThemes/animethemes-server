<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Performance;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Song\Performance as PerformanceFilament;
use App\Models\Wiki\Song\Performance;

/**
 * Class PerformanceDeleted.
 *
 * @extends WikiDeletedEvent<Performance>
 */
class PerformanceDeleted extends WikiDeletedEvent implements UpdateRelatedIndicesEvent
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
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        if ($this->getModel()->isMembership()) {
            return "Song '**{$this->getModel()->song->getName()}**' has been detached from Artist '**{$this->getModel()->artist->member->getName()}**' via Group '**{$this->getModel()->artist->artist->getName()}**'.";
        }

        return "Song '**{$this->getModel()->song->getName()}**' has been detached from Artist '**{$this->getModel()->artist->getName()}**'.";
    }

    /**
     * Get the message for the filament notification.
     *
     * @return string
     */
    protected function getNotificationMessage(): string
    {
        return "Performance '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the Filament notification.
     *
     * @return string
     */
    protected function getFilamentNotificationUrl(): string
    {
        $uriKey = PerformanceFilament::getRecordSlug();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices(): void
    {
        $performance = $this->getModel()->load([Performance::RELATION_ARTIST]);

        if ($performance->isMembership()) {
            $performance->artist->artist->searchable();
            $performance->artist->member->searchable();
        }

        $performance->artist->searchable();
    }
}
