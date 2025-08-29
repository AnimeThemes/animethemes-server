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

    public function shouldRender(Query $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria !== null && $criteria->isAllowedField($this->getKey());
    }

    public function render(Model $model): mixed
    {
        return $model->getAttribute($this->getColumn());
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria !== null && $criteria->isAllowedField($this->getKey());
    }

    /**
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
