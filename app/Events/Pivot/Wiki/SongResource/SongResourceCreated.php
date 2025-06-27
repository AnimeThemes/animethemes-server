<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\SongResource;

use App\Events\Base\Pivot\PivotCreatedEvent;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\SongResource;

/**
 * Class SongResourceCreated.
 *
 * @extends PivotCreatedEvent<Song, ExternalResource>
 */
class SongResourceCreated extends PivotCreatedEvent
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

        return "Resource '**{$foreign->getName()}**' has been attached to Song '**{$related->getName()}**'.";
    }
}
