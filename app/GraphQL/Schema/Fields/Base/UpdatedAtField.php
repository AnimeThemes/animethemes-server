<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Base;

use App\GraphQL\Schema\Fields\DateTimeTzField;
use App\Models\BaseModel;

class UpdatedAtField extends DateTimeTzField
{
    public function __construct(bool $nullable = true)
    {
        parent::__construct(BaseModel::ATTRIBUTE_UPDATED_AT, nullable: $nullable);
    }

    public function description(): string
    {
        return 'The date that the resource was updated';
    }
}
