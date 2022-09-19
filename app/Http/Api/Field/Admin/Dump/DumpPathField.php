<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Admin\Dump;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Field\StringField;
use App\Http\Resources\Admin\Resource\DumpResource;
use App\Models\Admin\Dump;
use Illuminate\Http\Request;

/**
 * Class DumpPathField.
 */
class DumpPathField extends StringField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Dump::ATTRIBUTE_PATH);
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  Criteria|null  $criteria
     * @return bool
     */
    public function shouldSelect(?Criteria $criteria): bool
    {
        // The link field is dependent on this field to build the url.
        return parent::shouldSelect($criteria) || $criteria->isAllowedField(DumpResource::ATTRIBUTE_LINK);
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
