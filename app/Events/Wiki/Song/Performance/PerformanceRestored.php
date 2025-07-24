<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Performance;

use App\Contracts\Events\SyncArtistSongEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use App\Pivots\Wiki\ArtistSong;
use Exception;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class PerformanceRestored.
 *
 * @extends WikiRestoredEvent<Performance>
 */
class PerformanceRestored extends WikiRestoredEvent implements SyncArtistSongEvent, UpdateRelatedIndicesEvent
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
        return "Performance '**{$this->getModel()->getName()}**' has been restored.";
    }

    /**
     * Perform cascading deletes.
     */
    public function updateRelatedIndices(): void
    {
        $performance = $this->getModel()->load([
            Performance::RELATION_ARTIST => function (MorphTo $morphTo) {
                $morphTo->morphWith([
                    Artist::class => [],
                    Membership::class => [Membership::RELATION_ARTIST, Membership::RELATION_MEMBER],
                ]);
            },
        ]);

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
            $artist->songs()->syncWithPivotValues([$song->getKey()], [[
                ArtistSong::ATTRIBUTE_ALIAS => $performance->alias,
                ArtistSong::ATTRIBUTE_AS => $performance->as,
            ]], false);
        });
    }
}
