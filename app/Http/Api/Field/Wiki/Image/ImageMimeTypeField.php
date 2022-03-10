<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Image;

use App\Http\Api\Field\StringField;
use App\Models\Wiki\Image;

/**
 * Class ImageMimeTypeField.
 */
class ImageMimeTypeField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Image::ATTRIBUTE_MIMETYPE);
    }
}
