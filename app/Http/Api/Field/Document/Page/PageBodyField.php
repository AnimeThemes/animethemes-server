<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Document\Page;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\RenderableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Document\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PageBodyField extends Field implements CreatableField, RenderableField, SelectableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Page::ATTRIBUTE_BODY);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'string',
            'max:16777215',
        ];
    }

    /**
     * Determine if the field should be displayed to the user.
     */
    public function shouldRender(Query $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria !== null && $criteria->isAllowedField($this->getKey());
    }

    /**
     * Get the value to display to the user.
     */
    public function render(Model $model): mixed
    {
        return $model->getAttribute($this->getColumn());
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     */
    public function shouldSelect(Query $query, Schema $schema): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria !== null && $criteria->isAllowedField($this->getKey());
    }

    /**
     * Set the update validation rules for the field.
     *
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'string',
            'max:16777215',
        ];
    }
}
