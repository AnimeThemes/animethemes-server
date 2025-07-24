<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\ArtistResource;

use App\Events\Base\Pivot\PivotUpdatedEvent;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\ArtistResource;

/**
 * Class ArtistResourceUpdated.
 *
 * @extends PivotUpdatedEvent<Artist, ExternalResource>
 */
class ArtistResourceUpdated extends PivotUpdatedEvent
{
    public function __construct(ArtistResource $artistResource)
    {
        parent::__construct($artistResource->artist, $artistResource->resource);
        $this->initializeEmbedFields($artistResource);
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Resource '**{$foreign->getName()}**' for Artist '**{$related->getName()}**' has been updated.";
    }
}
