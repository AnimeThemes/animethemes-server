<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\ArtistSong;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Pivot\PivotCreatedEvent;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;

/**
 * Class ArtistSongCreated.
 *
 * @extends PivotCreatedEvent<Artist, Song>
 */
class ArtistSongCreated extends PivotCreatedEvent implements UpdateRelatedIndicesEvent
{
    /**
     * Create a new event instance.
     *
     * @param  ArtistSong  $artistSong
     */
    public function __construct(ArtistSong $artistSong)
    {
        parent::__construct($artistSong->artist, $artistSong->song);
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

        return "Song '**{$foreign->getName()}**' has been attached to Artist '**{$related->getName()}**'.";
    }

    /**
     * Perform updates on related indices.
     */
    public function updateRelatedIndices(): void
    {
        // refresh artist document
        $artist = $this->getRelated();
        $artist->searchable();
    }
}
