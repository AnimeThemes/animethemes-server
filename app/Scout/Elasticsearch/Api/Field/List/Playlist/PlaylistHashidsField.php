<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\List\Playlist;

use App\Contracts\Models\HasHashids;
use App\Scout\Elasticsearch\Api\Field\StringField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class PlaylistHashidsField extends StringField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, HasHashids::ATTRIBUTE_HASHID);
    }
}
