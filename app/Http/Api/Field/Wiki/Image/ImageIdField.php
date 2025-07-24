<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Image;

use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Image;

class ImageIdField extends IdField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Image::ATTRIBUTE_ID);
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     */
    public function shouldSelect(Query $query, Schema $schema): bool
    {
        $includeCriteria = $query->getIncludeCriteria($this->schema->type());
        $linkField = new ImageLinkField($this->schema);
        if (
            $this->schema->type() === $schema->type()
            && ($includeCriteria === null || $includeCriteria->getPaths()->isEmpty())
        ) {
            return parent::shouldSelect($query, $schema) || $linkField->shouldRender($query);
        }

        return true;
    }
}
