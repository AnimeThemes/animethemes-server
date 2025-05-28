<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\Playlist;

use App\GraphQL\Definition\Fields\Base\ExistsField;
use App\Models\List\Playlist;

/**
 * Class PlaylistTracksExistsField.
 */
class PlaylistTracksExistsField extends ExistsField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Playlist::RELATION_TRACKS, 'tracksExists');
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The existence of tracks belonging to the resource';
    }
}
