<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\SongResource;

use App\Events\Base\Pivot\PivotUpdatedEvent;
use App\Models\Wiki\Song;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\SongResource;

/**
 * Class SongResourceUpdated.
 *
 * @extends PivotUpdatedEvent<Song, ExternalResource>
 */
class SongResourceUpdated extends PivotUpdatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  SongResource  $songResource
     */
    public function __construct(SongResource $songResource)
    {
        parent::__construct($songResource->song, $songResource->resource);
        $this->initializeEmbedFields($songResource);
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

        return "Resource '**{$foreign->getName()}**' for Song '**{$related->getName()}**' has been updated.";
    }
}
