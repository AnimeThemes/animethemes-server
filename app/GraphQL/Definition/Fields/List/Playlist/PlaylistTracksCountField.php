<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\Playlist;

use App\GraphQL\Definition\Fields\Base\CountField;
use App\Models\List\Playlist;

class PlaylistTracksCountField extends CountField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Playlist::RELATION_TRACKS, 'tracksCount');
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The number of tracks belonging to the resource';
    }
}
