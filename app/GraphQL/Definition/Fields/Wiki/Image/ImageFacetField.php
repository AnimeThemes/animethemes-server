<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\GraphQL\Definition\Fields\EnumField;
use App\Models\Wiki\Image;

class ImageFacetField extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Image::ATTRIBUTE_FACET, ImageFacet::class, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The component that the resource is intended for';
    }
}
