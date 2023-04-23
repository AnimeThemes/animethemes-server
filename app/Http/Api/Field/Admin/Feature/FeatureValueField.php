<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Admin\Feature;

use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Models\Admin\Feature;
use Illuminate\Http\Request;

/**
 * Class FeatureValueField.
 */
class FeatureValueField extends StringField implements UpdatableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Feature::ATTRIBUTE_VALUE);
    }

    /**
     * Set the update validation rules for the field.
     *
     * @param  Request  $request
     * @return array
     */
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
