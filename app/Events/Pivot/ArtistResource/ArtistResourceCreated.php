<?php

declare(strict_types=1);

namespace App\Events\Pivot\ArtistResource;

use App\Events\Base\Pivot\PivotCreatedEvent;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\ArtistResource;

/**
 * Class ArtistResourceCreated.
 *
 * @extends PivotCreatedEvent<Artist, ExternalResource>
 */
class ArtistResourceCreated extends PivotCreatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  ArtistResource  $artistResource
     */
    public function __construct(ArtistResource $artistResource)
    {
        parent::__construct($artistResource->artist, $artistResource->resource);
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

        return "Resource '**{$foreign->getName()}**' has been attached to Artist '**{$related->getName()}**'.";
    }
}
