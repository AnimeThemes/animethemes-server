<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Base;

use App\Models\BaseModel;
use App\Scout\Elasticsearch\Api\Field\DateField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

/**
 * Class DeletedAtField.
 */
class DeletedAtField extends DateField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, BaseModel::ATTRIBUTE_DELETED_AT);
    }
}
