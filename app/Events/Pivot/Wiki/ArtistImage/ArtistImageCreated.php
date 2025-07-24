<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\ArtistImage;

use App\Events\Base\Pivot\PivotCreatedEvent;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\ArtistImage;

/**
 * Class ArtistImageCreated.
 *
 * @extends PivotCreatedEvent<Artist, Image>
 */
class ArtistImageCreated extends PivotCreatedEvent
{
    public function __construct(ArtistImage $artistImage)
    {
        parent::__construct($artistImage->artist, $artistImage->image);
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Image '**{$foreign->getName()}**' has been attached to Artist '**{$related->getName()}**'.";
    }
}
