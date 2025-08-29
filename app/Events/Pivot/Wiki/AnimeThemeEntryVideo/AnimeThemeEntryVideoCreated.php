<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\AnimeThemeEntryVideo;

use App\Contracts\Events\UpdatePlaylistTracksEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Pivot\PivotCreatedEvent;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;

/**
 * @extends PivotCreatedEvent<AnimeThemeEntry, Video>
 */
class AnimeThemeEntryVideoCreated extends PivotCreatedEvent implements UpdatePlaylistTracksEvent, UpdateRelatedIndicesEvent
{
    public function __construct(AnimeThemeEntryVideo $entryVideo)
    {
        parent::__construct($entryVideo->animethemeentry, $entryVideo->video);
    }

    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Video '**{$foreign->getName()}**' has been attached to Entry '**{$related->getName()}**'.";
    }

    public function updateRelatedIndices(): void
    {
        // refresh video document
        $video = $this->getForeign();
        $video->searchable();
    }

    public function updatePlaylistTracks(): void
    {
        $entry = $this->getRelated();
        $video = $this->getForeign();

        // At the moment a video doesn't have any entries and a new pivot is created,
        // tracks should update the entry_id.
        PlaylistTrack::query()
            ->whereNull(PlaylistTrack::ATTRIBUTE_ENTRY)
            ->where(PlaylistTrack::ATTRIBUTE_VIDEO, $video->getKey())
            ->update([PlaylistTrack::ATTRIBUTE_ENTRY => $entry->getKey()]);
    }
}
