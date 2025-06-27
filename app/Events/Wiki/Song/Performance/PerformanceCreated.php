<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Performance;

use App\Contracts\Events\SyncArtistSongEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use App\Pivots\Wiki\ArtistSong;
use Exception;

/**
 * Class PerformanceCreated.
 *
 * @extends WikiCreatedEvent<Performance>
 */
class PerformanceCreated extends WikiCreatedEvent implements SyncArtistSongEvent, UpdateRelatedIndicesEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Performance  $performance
     */
    public function __construct(Performance $performance)
    {
        parent::__construct($performance);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Performance
     */
    public function getModel(): Performance
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        $performance = $this->getModel();
        $song = $performance->song;
        $artist = $performance->artist;

        if ($this->getModel()->isMembership()) {
            return "Song '**{$song->getName()}**' has been attached to Artist '**{$artist->member->getName()}**' as member of '**{$artist->artist->getName()}**'.";
        }

        return "Song '**{$song->getName()}**' has been attached to Artist '**{$artist->getName()}**'.";
    }

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices(): void
    {
        $performance = $this->getModel()->load([Performance::RELATION_ARTIST]);

        if ($performance->isMembership()) {
            $performance->artist->artist->searchable();
            $performance->artist->member->searchable();

            return;
        }

        $performance->artist->searchable();
    }

    /**
     * Sync the performance with the artist song.
     * Temporary function.
     *
     * @return void
     */
    public function syncArtistSong(): void
    {
        $performance = $this->getModel();

        $song = $performance->song;

        $artist = match ($performance->artist_type) {
            Artist::class => $performance->artist,
            Membership::class => $performance->artist->artist,
            default => throw new Exception('Invalid artist type.'),
        };

        ArtistSong::withoutEvents(function () use ($artist, $song, $performance) {
            $artist->songs()->syncWithPivotValues([$song->getKey()], [
                ArtistSong::ATTRIBUTE_ALIAS => $performance->alias,
                ArtistSong::ATTRIBUTE_AS => $performance->as,
            ], false);
        });
    }
}
