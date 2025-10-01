<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Admin\Feature;

use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Models\Admin\Feature;
use Illuminate\Http\Request;

class FeatureValueField extends StringField implements UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Feature::ATTRIBUTE_VALUE);
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'string',
            'max:192',
        ];
    }
}
