<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\Wiki\StudioResource;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Http\Request;

class StudioResourceAsField extends StringField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, StudioResource::ATTRIBUTE_AS);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'nullable',
            'string',
            'max:192',
        ];
    }

    /**
     * Set the update validation rules for the field.
     *
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        return [
            'nullable',
            'string',
            'max:192',
        ];
    }
}
