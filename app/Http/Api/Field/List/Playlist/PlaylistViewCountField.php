<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\Playlist;

use App\Http\Api\Field\CountField;
use App\Models\List\Playlist;

/**
 * Class PlaylistViewCountField.
 */
class PlaylistViewCountField extends CountField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Playlist::RELATION_VIEWS);
    }
}
