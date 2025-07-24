<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Artist;

use App\Models\Wiki\Artist;
use App\Scout\Elasticsearch\Api\Field\StringField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class ArtistSlugField extends StringField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Artist::ATTRIBUTE_SLUG);
    }
}
