<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\ExternalResource;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\IntField;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\Request;

/**
 * Class ExternalResourceIdField.
 */
class ExternalResourceIdField extends IntField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ExternalResource::ATTRIBUTE_EXTERNAL_ID);
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
            'integer',
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
            'integer',
        ];
    }
}
