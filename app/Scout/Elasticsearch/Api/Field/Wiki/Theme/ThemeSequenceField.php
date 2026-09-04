<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Theme;

use App\Models\Wiki\Theme;
use App\Scout\Elasticsearch\Api\Field\IntField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class ThemeSequenceField extends IntField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Theme::ATTRIBUTE_SEQUENCE);
    }
}
