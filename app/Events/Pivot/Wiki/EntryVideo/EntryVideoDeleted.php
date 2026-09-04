<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\EntryVideo;

use App\Contracts\Events\UpdatePlaylistTracksEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Pivot\PivotDeletedEvent;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\EntryVideo;

/**
 * @extends PivotDeletedEvent<Entry, Video>
 */
class EntryVideoDeleted extends PivotDeletedEvent implements UpdatePlaylistTracksEvent, UpdateRelatedIndicesEvent
{
    public function __construct(EntryVideo $entryVideo)
    {
        parent::__construct($entryVideo->animethemeentry, $entryVideo->video);
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

        // Try to find another entry attached to replace the detached entry.
        $newEntry = $video->animethemeentries()->first();

        PlaylistTrack::query()
            ->where(PlaylistTrack::ATTRIBUTE_ENTRY, $entry->getKey())
            ->where(PlaylistTrack::ATTRIBUTE_VIDEO, $video->getKey())
            ->update([PlaylistTrack::ATTRIBUTE_ENTRY => $newEntry?->getKey()]);
    }
}
