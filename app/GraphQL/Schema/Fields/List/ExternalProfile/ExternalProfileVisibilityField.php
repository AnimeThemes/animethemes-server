<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\List\ExternalProfile;

use App\Enums\Models\List\ExternalProfileVisibility;
use App\GraphQL\Schema\Fields\EnumField;
use App\Models\List\ExternalProfile;

class ExternalProfileVisibilityField extends EnumField
{
    public function __construct()
    {
        parent::__construct(ExternalProfile::ATTRIBUTE_VISIBILITY, ExternalProfileVisibility::class, nullable: false);
    }

    public function description(): string
    {
        return 'The state of who can see the profile';
    }
}
