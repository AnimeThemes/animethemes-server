<?php

declare(strict_types=1);

namespace App\Http\Api\Schema;

use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\SortableField;
use App\Contracts\Http\Api\Schema\SchemaInterface;
use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\DeletedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\TrashedFilter;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Sort\RandomSort;
use App\Http\Api\Sort\Sort;
use Illuminate\Support\Arr;

/**
 * Class Schema.
 */
abstract class Schema implements SchemaInterface
{
    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            new CreatedAtField($this),
            new UpdatedAtField($this),
            new DeletedAtField($this),
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
            ->push(new TrashedFilter())
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
            ->push(new RandomSort())
            ->all();
    }

    /**
     * Get the schema of the relation by path.
     *
     * @param  string  $path
     * @return Schema|null
     */
    public function relation(string $path): ?Schema
    {
        $relationInclude = Arr::first($this->allowedIncludes(), fn (AllowedInclude $include) => $include->path() === $path);

        return $relationInclude?->schema();
    }
}
