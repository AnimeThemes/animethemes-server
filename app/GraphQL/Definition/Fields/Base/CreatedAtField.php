<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Base;

use App\GraphQL\Attributes\Resolvers\UseFieldDirective;
use App\GraphQL\Definition\Fields\DateTimeTzField;
use App\GraphQL\Resolvers\PivotResolver;
use App\Models\BaseModel;

#[UseFieldDirective(PivotResolver::class)]
class CreatedAtField extends DateTimeTzField
{
    public function __construct()
    {
        parent::__construct(BaseModel::ATTRIBUTE_CREATED_AT);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The date that the resource was created';
    }
}
