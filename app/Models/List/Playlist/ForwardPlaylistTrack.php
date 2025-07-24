<?php

declare(strict_types=1);

namespace App\Models\List\Playlist;

class ForwardPlaylistTrack extends PlaylistTrack
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
        return PlaylistTrack::ATTRIBUTE_PREVIOUS;
    }
}
