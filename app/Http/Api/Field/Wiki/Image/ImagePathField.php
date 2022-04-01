<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Image;

use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Field\StringField;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Models\Wiki\Image;

/**
 * Class ImagePathField.
 */
class ImagePathField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Image::ATTRIBUTE_PATH);
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  Criteria|null  $criteria
     * @return bool
     */
    public function shouldSelect(?Criteria $criteria): bool
    {
        // The link field is dependent on this field to build the url.
        return parent::shouldSelect($criteria) || $criteria->isAllowedField(ImageResource::ATTRIBUTE_LINK);
    }
}
