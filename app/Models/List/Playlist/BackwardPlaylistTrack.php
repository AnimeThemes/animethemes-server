<?php

declare(strict_types=1);

namespace App\Models\List\Playlist;

class BackwardPlaylistTrack extends PlaylistTrack
{
    /**
     * Get the name of the parent key column.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getParentKeyName(): string
    {
        return PlaylistTrack::ATTRIBUTE_NEXT;
    }
}
