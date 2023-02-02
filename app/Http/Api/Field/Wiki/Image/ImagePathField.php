<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Image;

use App\Http\Api\Field\StringField;
use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Models\Wiki\Image;

/**
 * Class ImagePathField.
 */
class ImagePathField extends StringField
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Image::ATTRIBUTE_PATH);
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  ReadQuery  $query
     * @return bool
     */
    public function shouldSelect(ReadQuery $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        // The link field is dependent on this field to build the url.
        return parent::shouldSelect($query) || $criteria->isAllowedField(ImageResource::ATTRIBUTE_LINK);
    }
}
