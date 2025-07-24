<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\ArtistResource;

use App\Events\Base\Pivot\PivotDeletedEvent;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\ArtistResource;

/**
 * Class ArtistResourceDeleted.
 *
 * @extends PivotDeletedEvent<Artist, ExternalResource>
 */
class ArtistResourceDeleted extends PivotDeletedEvent
{
    public function __construct(ArtistResource $artistResource)
    {
        parent::__construct($artistResource->artist, $artistResource->resource);
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Resource '**{$foreign->getName()}**' has been detached from Artist '**{$related->getName()}**'.";
    }
}
