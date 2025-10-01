<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Image;

use App\Http\Api\Field\StringField;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Image;

class ImagePathField extends StringField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Image::ATTRIBUTE_PATH);
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());
        // The link field is dependent on this field to build the url.
        if (parent::shouldSelect($query, $schema)) {
            return true;
        }

        return $criteria->isAllowedField(Image::ATTRIBUTE_LINK);
    }
}
