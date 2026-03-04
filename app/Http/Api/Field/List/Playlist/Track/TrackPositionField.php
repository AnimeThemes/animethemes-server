<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\Playlist\Track;

use App\Http\Api\Field\IntField;
use App\Http\Api\Schema\Schema;
use App\Models\List\Playlist\PlaylistTrack;

class TrackPositionField extends IntField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, PlaylistTrack::ATTRIBUTE_POSITION);
    }
}
