<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\ExternalResource;

use App\GraphQL\Definition\Fields\IntField;
use App\Models\Wiki\ExternalResource;

class ExternalResourceExternalIdField extends IntField
{
    public function __construct()
    {
        parent::__construct(ExternalResource::ATTRIBUTE_EXTERNAL_ID);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The primary key of the resource in the external site';
    }
}
