<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\List\Playlist;

use App\Models\List\Playlist;
use App\Scout\Elasticsearch\Api\Field\StringField;

/**
 * Class PlaylistNameField.
 */
class PlaylistNameField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Playlist::ATTRIBUTE_NAME);
    }
}
