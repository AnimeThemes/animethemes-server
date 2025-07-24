<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\List\Playlist;

use App\Enums\Models\List\PlaylistVisibility;
use App\Models\List\Playlist;
use App\Scout\Elasticsearch\Api\Field\EnumField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class PlaylistVisibilityField extends EnumField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::class);
    }
}
