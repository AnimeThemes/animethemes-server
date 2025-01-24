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
use Illuminate\Support\Str;
use RuntimeException;

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

        /** @phpstan-ignore-next-line */
        return $relationInclude?->schema();
    }

    /**
     * Get the allowed includes by checking intermediate paths.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        $allowedIncludes = collect();

        foreach ($this->finalAllowedIncludes() as $finalAllowedInclude) {
            if (!$finalAllowedInclude->allowsIntermediate()) {
                // Skip to the whole path if it doesn't allow intermediate paths
                $allowedIncludes->put($finalAllowedInclude->path(), $finalAllowedInclude);
                continue;
            }

            $appendPath = Str::of('');
            foreach (explode('.', $finalAllowedInclude->path()) as $path) {
                $appendPath = $appendPath->append(empty($appendPath->__toString()) ? '' : '.', $path);

                $stringAppendPath = $appendPath->__toString();

                $schema = null;
                foreach ($this->finalAllowedIncludes() as $include) {
                    if ($include->path() === $stringAppendPath) {
                        $schema = $include->schema();
                        break;
                    }
                }

                if ($schema === null) {
                    $schema = $this->resolve($stringAppendPath);
                }

                $allowedIncludes->put($stringAppendPath, new AllowedInclude($schema, $stringAppendPath));
            }
        }

        return $allowedIncludes->values()->toArray();
    }

    /**
     * Resolve the schema by path.
     *
     * @param  string  $path
     * @return Schema
     *
     * @throws RuntimeException
     */
    protected function resolve(string $path): Schema
    {
        $model = Str::of(get_class($this))
            ->replace('Http\\Api\\Schema', 'Models')
            ->remove('Schema')
            ->__toString();

        if (!class_exists($model)) {
            return $this;
        }

        $classModel = new $model;

        foreach (explode('.', $path) as $path) {
            if (!method_exists($classModel, $path)) {
                $classBasename = get_class($classModel);
                throw new RuntimeException("Relation '$path' does not exist on model '$classBasename'.");
            }
            $classModel = $classModel->$path()->getRelated();
        }

        if (method_exists($classModel, 'getSchema')) {
            return $classModel->getSchema();
        }

        $schema = Str::of(get_class($classModel))
            ->replace('Models', 'Http\\Api\\Schema')
            ->append('Schema')
            ->__toString();

        if (!class_exists($schema)) {
            throw new RuntimeException("Schema class '$schema' does not exist.");
        }

        return new $schema;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    abstract protected function finalAllowedIncludes(): array;
}
