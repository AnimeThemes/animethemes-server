<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\ArtistSong;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Pivot\PivotUpdatedEvent;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;

/**
 * Class ArtistSongUpdated.
 *
 * @extends PivotUpdatedEvent<Artist, Song>
 */
class ArtistSongUpdated extends PivotUpdatedEvent implements UpdateRelatedIndicesEvent
{
    /**
     * Create a new event instance.
     *
     * @param  ArtistSong  $artistSong
     */
    public function __construct(ArtistSong $artistSong)
    {
        parent::__construct($artistSong->artist, $artistSong->song);
        $this->initializeEmbedFields($artistSong);
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

        return "Song '**{$foreign->getName()}**' for Artist '**{$related->getName()}**' has been updated.";
    }

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices(): void
    {
        // refresh artist document
        $artist = $this->getRelated();
        $artist->searchable();
    }
}
