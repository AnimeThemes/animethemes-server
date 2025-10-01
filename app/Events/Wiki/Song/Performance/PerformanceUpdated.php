<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Performance;

use App\Contracts\Events\SyncArtistSongEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use App\Pivots\Wiki\ArtistSong;
use Exception;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @extends WikiUpdatedEvent<Performance>
 */
class PerformanceUpdated extends WikiUpdatedEvent implements SyncArtistSongEvent, UpdateRelatedIndicesEvent
{
    public function __construct(Performance $performance)
    {
        parent::__construct($performance);
        $this->initializeEmbedFields($performance);
    }

    public function getModel(): Performance
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Performance '**{$this->getModel()->getName()}**' has been updated.";
    }

    public function updateRelatedIndices(): void
    {
        $performance = $this->getModel()->load([
            Performance::RELATION_ARTIST => function (MorphTo $morphTo): void {
                $morphTo->morphWith([
                    Artist::class => [],
                    Membership::class => [Membership::RELATION_GROUP, Membership::RELATION_MEMBER],
                ]);
            },
        ]);

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

        ArtistSong::withoutEvents(function () use ($artist, $song, $performance): void {
            ArtistSong::query()->where([
                ArtistSong::ATTRIBUTE_ARTIST => $artist->getKey(),
                ArtistSong::ATTRIBUTE_SONG => $song->getKey(),
            ])->update([
                ArtistSong::ATTRIBUTE_ALIAS => $performance->alias,
                ArtistSong::ATTRIBUTE_AS => $performance->as,
            ]);
        });
    }
}
