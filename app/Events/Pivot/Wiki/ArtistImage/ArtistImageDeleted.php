<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\ArtistImage;

use App\Events\Base\Pivot\PivotDeletedEvent;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\ArtistImage;

/**
 * Class ArtistImageDeleted.
 *
 * @extends PivotDeletedEvent<Artist, Image>
 */
class ArtistImageDeleted extends PivotDeletedEvent
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

        return "Image '**{$foreign->getName()}**' has been detached from Artist '**{$related->getName()}**'.";
    }
}
