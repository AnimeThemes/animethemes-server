<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\List\Playlist\PlaylistTrack;

use App\GraphQL\Schema\Fields\IntField;
use App\Models\List\Playlist\PlaylistTrack;

class PlaylistTrackPositionField extends IntField
{
    public function __construct()
    {
        parent::__construct(PlaylistTrack::ATTRIBUTE_POSITION, nullable: false);
    }

    public function description(): string
    {
        return 'The position of the playlist track within the playlist';
    }
}
