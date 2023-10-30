<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\SongResource;

use App\Events\Base\Pivot\PivotDeletedEvent;
use App\Models\Wiki\Song;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\SongResource;

/**
 * Class SongResourceDeleted.
 *
 * @extends PivotDeletedEvent<Song, ExternalResource>
 */
class SongResourceDeleted extends PivotDeletedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  SongResource  $songResource
     */
    public function __construct(SongResource $songResource)
    {
        parent::__construct($songResource->song, $songResource->resource);
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

        return "Resource '**{$foreign->getName()}**' has been detached from Song '**{$related->getName()}**'.";
    }
}
