<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Synonym;

use App\Models\Wiki\Synonym;
use App\Scout\Elasticsearch\Api\Field\StringField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class SynonymTextField extends StringField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Synonym::ATTRIBUTE_TEXT);
    }
}
