<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\ExternalProfile;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\List\ExternalProfile;

class ExternalProfileNameField extends StringField
{
    public function __construct()
    {
        parent::__construct(ExternalProfile::ATTRIBUTE_NAME, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The title of the profile';
    }
}
