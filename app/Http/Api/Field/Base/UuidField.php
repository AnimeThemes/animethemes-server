<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Base;

use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\RenderableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\StringFilter;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UuidField.
 */
class UuidField extends Field implements FilterableField, RenderableField, SelectableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     * @param  string  $column
     */
    public function __construct(Schema $schema, string $column)
    {
        parent::__construct($schema, BaseResource::ATTRIBUTE_ID, $column);
    }

    /**
     * Get the filter that can be applied to the field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new StringFilter($this->getKey(), $this->getColumn());
    }

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
        // We can only exclude ID fields for top-level models that are not including related resources.
        $includeCriteria = $query->getIncludeCriteria($this->schema->type());
        if (
            $this->schema->type() === $schema->type()
            && ($includeCriteria === null || $includeCriteria->getPaths()->isEmpty())
        ) {
            $criteria = $query->getFieldCriteria($this->schema->type());

            return $criteria === null || $criteria->isAllowedField($this->getKey());
        }

        return true;
    }
}
