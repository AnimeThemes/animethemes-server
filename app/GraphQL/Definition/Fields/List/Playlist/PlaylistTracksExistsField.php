<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\Playlist;

use App\GraphQL\Definition\Fields\Base\ExistsField;
use App\Models\List\Playlist;

class PlaylistTracksExistsField extends ExistsField
{
    public function __construct()
    {
        parent::__construct(Playlist::RELATION_TRACKS);
    }

    public function description(): string
    {
        return 'The existence of tracks belonging to the resource';
    }
}
