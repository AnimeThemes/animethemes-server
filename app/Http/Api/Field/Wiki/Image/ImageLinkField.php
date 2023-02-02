<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Image;

use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\Wiki\Resource\ImageResource;

/**
 * Class ImageLinkField.
 */
class ImageLinkField extends Field
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ImageResource::ATTRIBUTE_LINK);
    }
}
