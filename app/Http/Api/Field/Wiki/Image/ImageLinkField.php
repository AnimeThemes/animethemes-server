<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Image;

use App\Http\Api\Field\Field;
use App\Http\Resources\Wiki\Resource\ImageResource;

/**
 * Class ImageLinkField.
 */
class ImageLinkField extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ImageResource::ATTRIBUTE_LINK);
    }
}
