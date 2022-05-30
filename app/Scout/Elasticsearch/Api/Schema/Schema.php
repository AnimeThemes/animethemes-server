<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Schema;

use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\SortableField;
use App\Contracts\Http\Api\Schema\SchemaInterface;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Sort\Sort;
use App\Scout\Elasticsearch\Api\Field\Base\CreatedAtField;
use App\Scout\Elasticsearch\Api\Field\Base\DeletedAtField;
use App\Scout\Elasticsearch\Api\Field\Base\UpdatedAtField;
use App\Scout\Elasticsearch\Api\Field\Field;

/**
 * Class Schema.
 */
abstract class Schema implements SchemaInterface
{
    /**
     * The model this schema represents.
     *
     * @return string
     */
    abstract public function model(): string;

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    abstract public function allowedIncludes(): array;

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }

    /**
     * Get the filters of the resource.
     *
     * @return Filter[]
     */
    public function filters(): array
    {
        return collect($this->fields())
            ->filter(fn (Field $field) => $field instanceof FilterableField)
            ->map(fn (FilterableField $field) => $field->getFilter())
            ->all();
    }

    /**
     * Get the sorts of the resource.
     *
     * @return Sort[]
     */
    public function sorts(): array
    {
        return collect($this->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->all();
    }
}
