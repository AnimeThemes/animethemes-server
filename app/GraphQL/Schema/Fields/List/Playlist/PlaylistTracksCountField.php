<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\List\Playlist;

use App\GraphQL\Schema\Fields\Base\CountField;
use App\Models\List\Playlist;

class PlaylistTracksCountField extends CountField
{
    public function __construct()
    {
        parent::__construct(Playlist::RELATION_TRACKS, 'tracksCount');
    }

    public function description(): string
    {
        return 'The number of tracks belonging to the resource';
    }
}
