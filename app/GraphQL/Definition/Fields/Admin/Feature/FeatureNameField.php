<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Admin\Feature;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Admin\Feature;

class FeatureNameField extends StringField
{
    public function __construct()
    {
        parent::__construct(Feature::ATTRIBUTE_NAME, nullable: false);
    }

    public function description(): string
    {
        return 'The title of the resource';
    }
}
