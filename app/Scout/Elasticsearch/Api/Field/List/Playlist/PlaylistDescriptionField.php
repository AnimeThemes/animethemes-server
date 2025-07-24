<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\List\Playlist;

use App\Models\List\Playlist;
use App\Scout\Elasticsearch\Api\Field\StringField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class PlaylistDescriptionField extends StringField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Playlist::ATTRIBUTE_DESCRIPTION);
    }
}
