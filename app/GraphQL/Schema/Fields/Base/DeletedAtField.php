<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Base;

use App\Constants\ModelConstants;
use App\Contracts\GraphQL\Fields\DeprecatedField;
use App\GraphQL\Schema\Fields\DateTimeTzField;

class DeletedAtField extends DateTimeTzField implements DeprecatedField
{
    public function __construct()
    {
        parent::__construct(ModelConstants::ATTRIBUTE_DELETED_AT);
    }

    public function description(): string
    {
        return 'The date that the resource was deleted';
    }

    public function deprecationReason(): string
    {
        return 'It\'ll be removed alongside its related filters as this field is for internal use only';
    }
}
