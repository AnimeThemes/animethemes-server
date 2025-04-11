<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Contracts\Http\Api\Field\RenderableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use Illuminate\Database\Eloquent\Model;

/**
 * Class JsonField.
 */
abstract class JsonField extends Field implements RenderableField, SelectableField
{
    /**
     * Determine if the field should be displayed to the user.
     *
     * @param  Query  $query
     * @return bool
     */
    public function shouldRender(Query $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria === null || $criteria->isAllowedField($this->getKey());
    }

    /**
     * Get the value to display to the user.
     *
     * @param  Model  $model
     * @return mixed
     */
    public function render(Model $model): mixed
    {
        return $model->getAttribute($this->getColumn());
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  Query  $query
     * @param  Schema  $schema
     * @return bool
     */
    public function shouldSelect(Query $query, Schema $schema): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria === null || $criteria->isAllowedField($this->getKey());
    }
}
