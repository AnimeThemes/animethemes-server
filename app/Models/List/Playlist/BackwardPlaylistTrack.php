<?php

declare(strict_types=1);

namespace App\Models\List\Playlist;

/**
 * Class BackwardPlaylistTrack.
 */
class BackwardPlaylistTrack extends PlaylistTrack
{
    /**
     * Get the name of the parent key column.
     *
     * @return string
     */
    public function getParentKeyName(): string
    {
        return PlaylistTrack::ATTRIBUTE_NEXT;
    }
}
