<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\List\ExternalProfile;

use App\GraphQL\Schema\Fields\StringField;
use App\Models\List\ExternalProfile;

class ExternalProfileNameField extends StringField
{
    public function __construct()
    {
        parent::__construct(ExternalProfile::ATTRIBUTE_NAME, nullable: false);
    }

    public function description(): string
    {
        return 'The title of the profile';
    }
}
