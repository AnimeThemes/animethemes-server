<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Image;

use App\Contracts\Http\Api\Field\RenderableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Model;

class ImageLinkField extends Field implements RenderableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Image::ATTRIBUTE_LINK);
    }

    public function shouldRender(Query $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria === null || $criteria->isAllowedField($this->getKey());
    }

    /**
     * Get the value to display to the user.
     */
    public function render(Model $model): string
    {
        return $model->getAttribute($this->getColumn());
    }
}
