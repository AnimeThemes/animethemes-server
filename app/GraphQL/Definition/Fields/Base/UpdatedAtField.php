<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Base;

use App\GraphQL\Definition\Fields\DateTimeTzField;
use App\Models\BaseModel;

class UpdatedAtField extends DateTimeTzField
{
    public function __construct()
    {
        parent::__construct(BaseModel::ATTRIBUTE_UPDATED_AT);
    }

    public function description(): string
    {
        return 'The date that the resource was updated';
    }
}
