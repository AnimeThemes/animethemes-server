<?php

declare(strict_types=1);

namespace App\Models\List\Playlist;

use Illuminate\Database\Eloquent\Attributes\Table;

#[Table(PlaylistTrack::TABLE, PlaylistTrack::ATTRIBUTE_ID)]
class ForwardPlaylistTrack extends PlaylistTrack
{
    /**
     * Get the name of the parent key column.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getParentKeyName(): string
    {
        return PlaylistTrack::ATTRIBUTE_PREVIOUS;
    }
}
