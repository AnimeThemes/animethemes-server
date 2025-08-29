<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\Playlist\Track;

use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\List\Playlist\PlaylistTrack;

class TrackPlaylistIdField extends Field implements SelectableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, PlaylistTrack::ATTRIBUTE_PLAYLIST);
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match playlist relation.
        return true;
    }
}
