<?php

declare(strict_types=1);

namespace App\Events\Pivot\StudioImage;

use App\Events\Base\Pivot\PivotDeletedEvent;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\StudioImage;

/**
 * Class StudioImageDeleted.
 *
 * @extends PivotDeletedEvent<Studio, Image>
 */
class StudioImageDeleted extends PivotDeletedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  StudioImage  $studioImage
     */
    public function __construct(StudioImage $studioImage)
    {
        parent::__construct($studioImage->studio, $studioImage->image);
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

        return "Image '**{$foreign->getName()}**' has been detached from Studio '**{$related->getName()}**'.";
    }
}
