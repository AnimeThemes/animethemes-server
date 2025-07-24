<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Anime;

use App\Models\Wiki\Anime;
use App\Scout\Elasticsearch\Api\Field\StringField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class AnimeNameField extends StringField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Anime::ATTRIBUTE_NAME);
    }
}
