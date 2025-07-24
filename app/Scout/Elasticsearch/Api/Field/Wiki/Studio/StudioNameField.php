<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Studio;

use App\Models\Wiki\Studio;
use App\Scout\Elasticsearch\Api\Field\StringField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class StudioNameField extends StringField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Studio::ATTRIBUTE_NAME);
    }
}
