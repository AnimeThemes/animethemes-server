<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\ArtistResource;

use App\Events\Base\Pivot\PivotCreatedEvent;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\ArtistResource;

/**
 * Class ArtistResourceCreated.
 *
 * @extends PivotCreatedEvent<Artist, ExternalResource>
 */
class ArtistResourceCreated extends PivotCreatedEvent
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

        return "Resource '**{$foreign->getName()}**' has been attached to Artist '**{$related->getName()}**'.";
    }
}
