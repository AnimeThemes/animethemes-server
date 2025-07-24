<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\Playlist;

use App\Http\Api\Field\Aggregate\CountField;
use App\Http\Api\Schema\Schema;
use App\Models\List\Playlist;

class PlaylistTrackCountField extends CountField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Playlist::RELATION_TRACKS);
    }
}
