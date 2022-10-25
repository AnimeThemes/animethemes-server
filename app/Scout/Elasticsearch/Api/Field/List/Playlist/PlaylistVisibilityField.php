<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\List\Playlist;

use App\Enums\Models\List\PlaylistVisibility;
use App\Models\List\Playlist;
use App\Scout\Elasticsearch\Api\Field\EnumField;

/**
 * Class PlaylistVisibilityField.
 */
class PlaylistVisibilityField extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::class);
    }
}
