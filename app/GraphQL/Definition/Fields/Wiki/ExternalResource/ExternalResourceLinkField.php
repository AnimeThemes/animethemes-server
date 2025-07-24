<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\ExternalResource;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\ExternalResource;

class ExternalResourceLinkField extends StringField
{
    public function __construct()
    {
        parent::__construct(ExternalResource::ATTRIBUTE_LINK, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The URL of the external site';
    }
}
