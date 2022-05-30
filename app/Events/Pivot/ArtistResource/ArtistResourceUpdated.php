<?php

declare(strict_types=1);

namespace App\Events\Pivot\ArtistResource;

use App\Events\Base\Pivot\PivotUpdatedEvent;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\ArtistResource;

/**
 * Class ArtistResourceUpdated.
 *
 * @extends PivotUpdatedEvent<Artist, ExternalResource>
 */
class ArtistResourceUpdated extends PivotUpdatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  ArtistResource  $artistResource
     */
    public function __construct(ArtistResource $artistResource)
    {
        parent::__construct($artistResource->artist, $artistResource->resource);
        $this->initializeEmbedFields($artistResource);
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

        return "Resource '**{$foreign->getName()}**' for Artist '**{$related->getName()}**' has been updated.";
    }
}
