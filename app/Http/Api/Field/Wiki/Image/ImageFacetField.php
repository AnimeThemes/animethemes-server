<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Http\Api\Field\EnumField;
use App\Models\Wiki\Image;

/**
 * Class ImageFacetField.
 */
class ImageFacetField extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Image::ATTRIBUTE_FACET, ImageFacet::class);
    }
}
