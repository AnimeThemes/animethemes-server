<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Base;

use App\Constants\ModelConstants;
use App\Scout\Elasticsearch\Api\Field\DateField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class DeletedAtField extends DateField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ModelConstants::ATTRIBUTE_DELETED_AT);
    }
}
