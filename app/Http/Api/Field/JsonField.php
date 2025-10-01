<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Contracts\Http\Api\Field\RenderableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use Illuminate\Database\Eloquent\Model;

abstract class JsonField extends Field implements RenderableField, SelectableField
{
    public function shouldRender(Query $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return ! $criteria instanceof Criteria || $criteria->isAllowedField($this->getKey());
    }

    public function render(Model $model): mixed
    {
        return $model->getAttribute($this->getColumn());
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return ! $criteria instanceof Criteria || $criteria->isAllowedField($this->getKey());
    }
}
