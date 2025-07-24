<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\StudioResource;

use App\Events\Base\Pivot\PivotDeletedEvent;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;

/**
 * Class StudioResourceDeleted.
 *
 * @extends PivotDeletedEvent<Studio, ExternalResource>
 */
class StudioResourceDeleted extends PivotDeletedEvent
{
    public function __construct(StudioResource $studioResource)
    {
        parent::__construct($studioResource->studio, $studioResource->resource);
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Resource '**{$foreign->getName()}**' has been detached from Studio '**{$related->getName()}**'.";
    }
}
