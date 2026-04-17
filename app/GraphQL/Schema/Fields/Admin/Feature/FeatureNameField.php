<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Admin\Feature;

use App\Contracts\GraphQL\Fields\DeprecatedField;
use App\GraphQL\Schema\Fields\StringField;
use App\Models\Admin\Feature;

class FeatureNameField extends StringField implements DeprecatedField
{
    public function __construct()
    {
        parent::__construct(Feature::ATTRIBUTE_NAME, nullable: false);
    }

    public function description(): string
    {
        return 'The title of the resource';
    }

    public function deprecationReason(): string
    {
        return 'Internal use only';
    }
}
