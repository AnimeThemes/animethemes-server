<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Synonym;

use App\Enums\Models\Wiki\SynonymType;
use App\Models\Wiki\Synonym;
use App\Scout\Elasticsearch\Api\Field\EnumField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class SynonymTypeField extends EnumField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Synonym::ATTRIBUTE_TYPE, SynonymType::class);
    }
}
