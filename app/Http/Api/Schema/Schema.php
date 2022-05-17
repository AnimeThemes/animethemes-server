<?php

declare(strict_types=1);

namespace App\Http\Api\Schema;

use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\SortableField;
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
abstract class Schema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    abstract public function type(): string;

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
        $filters = [];

        foreach ($this->fields() as $field) {
            if ($field instanceof FilterableField) {
                $filters[] = $field->getFilter();
            }
        }

        $filters[] = new TrashedFilter();

        return $filters;
    }

    /**
     * Get the sorts of the resource.
     *
     * @return Sort[]
     */
    public function sorts(): array
    {
        $sorts = [];

        foreach ($this->fields() as $field) {
            if ($field instanceof SortableField) {
                $sorts[] = $field->getSort();
            }
        }

        $sorts[] = new RandomSort();

        return $sorts;
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
