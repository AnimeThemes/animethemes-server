<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Song;

use App\Models\Wiki\Song;
use App\Scout\Elasticsearch\Api\Field\StringField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class SongTitleField extends StringField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Song::ATTRIBUTE_TITLE);
    }
}
