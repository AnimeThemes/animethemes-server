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
 * @extends WikiDeletedEvent<Performance>
 */
class PerformanceDeleted extends WikiDeletedEvent implements SyncArtistSongEvent, UpdateRelatedIndicesEvent
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

            return "Song '**{$song->getName()}**' has been detached from Member '**{$memberName}**' of '**{$groupName}**'.";
        }

        return "Song '**{$song->getName()}**' has been detached from Artist '**{$artistName}**'.";
    }

    protected function getNotificationMessage(): string
    {
        return "Performance '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return PerformanceFilament::getUrl('view', ['record' => $this->getModel()]);
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

        ArtistSong::withoutEvents(function () use ($artist, $song): void {
            ArtistSong::query()->where([
                ArtistSong::ATTRIBUTE_ARTIST => $artist->getKey(),
                ArtistSong::ATTRIBUTE_SONG => $song->getKey(),
            ])->delete();
        });
    }
}
