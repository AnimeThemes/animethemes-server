<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\EntryVideo;

use App\Contracts\Events\UpdatePlaylistTracksEvent;
use App\Events\Base\Pivot\PivotDeletedEvent;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\EntryVideo;

/**
 * @extends PivotDeletedEvent<Entry, Video>
 */
class EntryVideoDeleted extends PivotDeletedEvent implements UpdatePlaylistTracksEvent
{
    public function __construct(EntryVideo $entryVideo)
    {
        parent::__construct($entryVideo->animethemeentry, $entryVideo->video);
    }

    public function updatePlaylistTracks(): void
    {
        $entry = $this->getRelated();
        $video = $this->getForeign();

        // Try to find another entry attached to replace the detached entry.
        $newEntry = $video->entries()->first();

        PlaylistTrack::query()
            ->where(PlaylistTrack::ATTRIBUTE_ENTRY, $entry->getKey())
            ->where(PlaylistTrack::ATTRIBUTE_VIDEO, $video->getKey())
            ->update([PlaylistTrack::ATTRIBUTE_ENTRY => $newEntry?->getKey()]);
    }
}
