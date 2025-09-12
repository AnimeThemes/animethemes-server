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
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @extends WikiCreatedEvent<Performance>
 */
class PerformanceCreated extends WikiCreatedEvent implements SyncArtistSongEvent, UpdateRelatedIndicesEvent
{
    public function __construct(Performance $performance)
    {
        parent::__construct($performance);
    }

    public function getModel(): Performance
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        $performance = $this->getModel();

        $song = $performance->song;
        $artist = $performance->artist;

        $artistName = $performance->alias ?? $artist->getName();
        $artistName = filled($performance->as) ? "{$performance->as} (CV: {$artistName})" : $artistName;

        if ($this->getModel()->isMembership()) {
            $groupName = $artistName;
            $membership = $artist;

            $memberName = $membership->alias ?? $membership->member->getName();
            $memberName = filled($membership->as) ? "{$membership->as} (CV: {$memberName})" : $memberName;

            return "Song '**{$song->getName()}**' has been attached to Member '**{$memberName}**' of '**{$groupName}**'.";
        }

        return "Song '**{$song->getName()}**' has been attached to Artist '**{$artistName}**'.";
    }

    public function updateRelatedIndices(): void
    {
        $performance = $this->getModel()->load([Performance::RELATION_ARTIST]);

        if ($performance->isMembership()) {
            $performance->artist->group->searchable();
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

        $artist = match (Relation::getMorphedModel($performance->artist_type)) {
            Artist::class => $performance->artist,
            Membership::class => $performance->artist->group,
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
