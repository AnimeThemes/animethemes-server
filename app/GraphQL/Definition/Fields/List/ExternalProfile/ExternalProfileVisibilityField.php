<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\ExternalProfile;

use App\Enums\Models\List\ExternalProfileVisibility;
use App\GraphQL\Definition\Fields\EnumField;
use App\Models\List\ExternalProfile;

/**
 * Class ExternalProfileVisibilityField.
 */
class ExternalProfileVisibilityField extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ExternalProfile::ATTRIBUTE_VISIBILITY, ExternalProfileVisibility::class, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The state of who can see the profile';
    }
}
