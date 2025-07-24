<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Admin\Feature;

use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Models\Admin\Feature;

class FeatureNameField extends StringField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Feature::ATTRIBUTE_NAME);
    }
}
