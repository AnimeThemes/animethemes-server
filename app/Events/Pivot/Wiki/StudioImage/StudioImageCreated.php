<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\StudioImage;

use App\Events\Base\Pivot\PivotCreatedEvent;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioImage;

/**
 * Class StudioImageCreated.
 *
 * @extends PivotCreatedEvent<Studio, Image>
 */
class StudioImageCreated extends PivotCreatedEvent
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

        return "Image '**{$foreign->getName()}**' has been attached to Studio '**{$related->getName()}**'.";
    }
}
