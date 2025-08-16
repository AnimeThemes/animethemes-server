<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\Morph\Imageable;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\IntField;
use App\Http\Api\Schema\Schema;
use App\Pivots\Morph\Imageable;
use Illuminate\Http\Request;

class ImageableDepthField extends IntField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Imageable::ATTRIBUTE_DEPTH);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'integer',
            'min:1',
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
            'sometimes',
            'required',
            'integer',
            'min:1',
        ];
    }
}
