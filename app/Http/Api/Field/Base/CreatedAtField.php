<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Base;

use App\Http\Api\Field\DateField;
use App\Http\Api\Schema\Schema;
use App\Models\BaseModel;

/**
 * Class CreatedAtField.
 */
class CreatedAtField extends DateField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, BaseModel::ATTRIBUTE_CREATED_AT);
    }
}
