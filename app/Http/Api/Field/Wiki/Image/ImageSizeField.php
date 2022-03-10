<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Image;

use App\Http\Api\Field\IntField;
use App\Models\Wiki\Image;

/**
 * Class ImageSizeField.
 */
class ImageSizeField extends IntField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Image::ATTRIBUTE_SIZE);
    }
}
