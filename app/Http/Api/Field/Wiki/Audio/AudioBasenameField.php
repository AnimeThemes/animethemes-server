<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Audio;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Field\StringField;
use App\Http\Resources\Wiki\Resource\AudioResource;
use App\Models\Wiki\Audio;
use Illuminate\Http\Request;

/**
 * Class AudioBasenameField.
 */
class AudioBasenameField extends StringField implements CreatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Audio::ATTRIBUTE_BASENAME);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  Request  $request
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'string',
            'max:192',
        ];
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  Criteria|null  $criteria
     * @return bool
     */
    public function shouldSelect(?Criteria $criteria): bool
    {
        // The link field is dependent on this field to build the route.
        return parent::shouldSelect($criteria) || $criteria->isAllowedField(AudioResource::ATTRIBUTE_LINK);
    }
}
