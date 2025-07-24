<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\Playlist;

use App\Http\Api\Field\Aggregate\ExistsField;
use App\Http\Api\Schema\Schema;
use App\Models\List\Playlist;

class PlaylistTrackExistsField extends ExistsField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Playlist::RELATION_TRACKS);
    }
}
