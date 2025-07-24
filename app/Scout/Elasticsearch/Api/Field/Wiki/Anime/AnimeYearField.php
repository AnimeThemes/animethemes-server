<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Anime;

use App\Models\Wiki\Anime;
use App\Scout\Elasticsearch\Api\Field\IntField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class AnimeYearField extends IntField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Anime::ATTRIBUTE_YEAR);
    }
}
