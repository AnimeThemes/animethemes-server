<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Audio;

use App\Contracts\Http\Api\Field\RenderableField;
use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Audio;
use Illuminate\Database\Eloquent\Model;

class AudioLinkField extends Field implements RenderableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Audio::ATTRIBUTE_LINK);
    }

    public function shouldRender(Query $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return ! $criteria instanceof Criteria || $criteria->isAllowedField($this->getKey());
    }

    public function render(Model $model): string
    {
        return $model->getAttribute($this->getColumn());
    }
}
