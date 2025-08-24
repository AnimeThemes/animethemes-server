<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Performance;

use App\Contracts\Events\SyncArtistSongEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Song\Performance as PerformanceFilament;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use App\Pivots\Wiki\ArtistSong;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class PerformanceDeleted.
 *
 * @extends WikiDeletedEvent<Performance>
 */
class PerformanceDeleted extends WikiDeletedEvent implements SyncArtistSongEvent, UpdateRelatedIndicesEvent
{
    public function __construct(Performance $performance)
    {
        parent::__construct($performance);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Performance
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        $performance = $this->getModel();
        $song = $performance->song;
        $artist = $performance->artist;

        if ($performance->isMembership()) {
            return "Song '**{$song->getName()}**' has been detached from Artist '**{$artist->member->getName()}**' via Group '**{$artist->group->getName()}**'.";
        }

        return "Song '**{$song->getName()}**' has been detached from Artist '**{$artist->getName()}**'.";
    }

    /**
     * Get the message for the filament notification.
     */
    protected function getNotificationMessage(): string
    {
        return "Performance '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the Filament notification.
     */
    protected function getFilamentNotificationUrl(): string
    {
        $uriKey = PerformanceFilament::getRecordSlug();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }

    /**
     * Perform updates on related indices.
     */
    public function updateRelatedIndices(): void
    {
        $performance = $this->getModel()->load([
            Performance::RELATION_ARTIST => function (MorphTo $morphTo) {
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

        if (
            $performance->artist_type === Relation::getMorphAlias(Membership::class)
            && Performance::query()
                ->whereBelongsTo($song)
                ->where(Performance::ATTRIBUTE_ARTIST_TYPE, Relation::getMorphAlias(Membership::class))
                ->whereHas(Performance::RELATION_ARTIST, fn (Builder $query) => $query->where(Artist::ATTRIBUTE_ID, $performance->artist->group->getKey()))
                ->exists()
        ) {
            return;
        }

        $artist = match (Relation::getMorphedModel($performance->artist_type)) {
            Artist::class => $performance->artist,
            Membership::class => $performance->artist->group,
            default => throw new Exception('Invalid artist type.'),
        };

        ArtistSong::withoutEvents(function () use ($artist, $song) {
            ArtistSong::query()->where([
                ArtistSong::ATTRIBUTE_ARTIST => $artist->getKey(),
                ArtistSong::ATTRIBUTE_SONG => $song->getKey(),
            ])->delete();
        });
    }
}
