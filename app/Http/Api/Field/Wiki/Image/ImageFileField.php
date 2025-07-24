<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Image;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;
use Illuminate\Http\Request;

class ImageFileField extends Field implements CreatableField
{
    final public const ATTRIBUTE_FILE = 'file';

    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ImageFileField::ATTRIBUTE_FILE);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'image',
        ];
    }
}
