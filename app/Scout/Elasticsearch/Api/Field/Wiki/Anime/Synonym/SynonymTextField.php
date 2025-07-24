<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Anime\Synonym;

use App\Models\Wiki\Anime\AnimeSynonym;
use App\Scout\Elasticsearch\Api\Field\StringField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class SynonymTextField extends StringField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeSynonym::ATTRIBUTE_TEXT);
    }
}
