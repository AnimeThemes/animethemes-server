<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\AnimeThemeEntryVideo;

use App\Contracts\Events\UpdatePlaylistTracksEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Pivot\PivotDeletedEvent;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;

/**
 * @extends PivotDeletedEvent<AnimeThemeEntry, Video>
 */
class AnimeThemeEntryVideoDeleted extends PivotDeletedEvent implements UpdatePlaylistTracksEvent, UpdateRelatedIndicesEvent
{
    public function __construct(AnimeThemeEntryVideo $entryVideo)
    {
        parent::__construct($entryVideo->animethemeentry, $entryVideo->video);
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Video '**{$foreign->getName()}**' has been detached from Entry '**{$related->getName()}**'.";
    }

    /**
     * Perform updates on related indices.
     */
    public function updateRelatedIndices(): void
    {
        // refresh video document
        $video = $this->getForeign();
        $video->searchable();
    }

    /**
     * Update the related playlist tracks.
     */
    public function updatePlaylistTracks(): void
    {
        $entry = $this->getRelated();
        $video = $this->getForeign();

        // Try to find another entry attached to replace the detached entry.
        $newEntry = $video->animethemeentries()->first();

        PlaylistTrack::query()
            ->where(PlaylistTrack::ATTRIBUTE_ENTRY, $entry->getKey())
            ->where(PlaylistTrack::ATTRIBUTE_VIDEO, $video->getKey())
            ->update([PlaylistTrack::ATTRIBUTE_ENTRY => $newEntry?->getKey()]);
    }
}
