<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Base;

use App\GraphQL\Attributes\UseField;
use App\GraphQL\Definition\Fields\DateTimeTzField;
use App\GraphQL\Resolvers\PivotResolver;
use App\Models\BaseModel;

#[UseField(PivotResolver::class)]
class UpdatedAtField extends DateTimeTzField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(BaseModel::ATTRIBUTE_UPDATED_AT);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The date that the resource was updated';
    }
}
