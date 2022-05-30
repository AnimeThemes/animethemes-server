<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Image;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Http\Api\Field\Field;
use Illuminate\Http\Request;

/**
 * Class ImageFileField.
 */
class ImageFileField extends Field implements CreatableField
{
    final public const ATTRIBUTE_FILE = 'file';

    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ImageFileField::ATTRIBUTE_FILE);
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
            'image',
        ];
    }
}
