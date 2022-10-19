<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\StudioResource;

use App\Events\Base\Pivot\PivotUpdatedEvent;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;

/**
 * Class StudioResourceUpdated.
 *
 * @extends PivotUpdatedEvent<Studio, ExternalResource>
 */
class StudioResourceUpdated extends PivotUpdatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  StudioResource  $studioResource
     */
    public function __construct(StudioResource $studioResource)
    {
        parent::__construct($studioResource->studio, $studioResource->resource);
        $this->initializeEmbedFields($studioResource);
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Resource '**{$foreign->getName()}**' for Studio '**{$related->getName()}**' has been updated.";
    }
}
