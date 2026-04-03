<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Performance;

use App\Contracts\Events\CreateArtistSongEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Song\Performance;
use App\Pivots\Wiki\ArtistSong;

/**
 * @extends WikiRestoredEvent<Performance>
 */
class PerformanceRestored extends WikiRestoredEvent implements CreateArtistSongEvent, UpdateRelatedIndicesEvent
{
    public function updateRelatedIndices(): void
    {
        $performance = $this->getModel()->load([
            Performance::RELATION_ARTIST,
            Performance::RELATION_MEMBER,
        ]);

        $performance->artist->searchable();
        $performance->member?->searchable();
    }

    public function createArtistSong(): void
    {
        $performance = $this->getModel();

        ArtistSong::withoutEvents(function () use ($performance): void {
            $performance->song->artists()->syncWithoutDetaching([
                $performance->artist_id => [
                    ArtistSong::ATTRIBUTE_ALIAS => $performance->alias,
                    ArtistSong::ATTRIBUTE_AS => $performance->as,
                ],
            ]);
        });
    }
}
