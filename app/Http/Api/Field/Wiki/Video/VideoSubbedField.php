<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Field\BooleanField;
use App\Models\Wiki\Video;
use Illuminate\Http\Request;

/**
 * Class VideoSubbedField.
 */
class VideoSubbedField extends BooleanField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_SUBBED);
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
            'sometimes',
            'required',
            'boolean',
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
        // The tags attribute is dependent on this field.
        return parent::shouldSelect($criteria) || $criteria->isAllowedField(Video::ATTRIBUTE_TAGS);
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
            'boolean',
        ];
    }
}
