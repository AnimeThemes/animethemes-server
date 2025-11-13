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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RuntimeException;

abstract class Schema implements SchemaInterface
{
    /**
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
     * @return Filter[]
     */
    public function filters(): array
    {
        return collect($this->fields())
            ->filter(fn (Field $field): bool => $field instanceof FilterableField)
            ->map(fn (FilterableField $field): Filter => $field->getFilter())
            ->all();
    }

    /**
     * @return Sort[]
     */
    public function sorts(): array
    {
        return collect($this->fields())
            ->filter(fn (Field $field): bool => $field instanceof SortableField)
            ->map(fn (SortableField $field): Sort => $field->getSort())
            ->all();
    }

    /**
     * Merge the allowed includes with intermediate paths.
     *
     * @param  AllowedInclude[]  $allowedIncludesToMerge
     * @return AllowedInclude[]
     */
    public function withIntermediatePaths(array $allowedIncludesToMerge = []): array
    {
        $allowedIncludes = collect();

        foreach ($allowedIncludesToMerge as $allowedInclude) {
            // When a path doesn't have intermediate paths
            if ($allowedInclude->isDirectRelation()) {
                $allowedIncludes->put($allowedInclude->path(), $allowedInclude);
                continue;
            }

            $appendPath = Str::of('');
            foreach (explode('.', $allowedInclude->path()) as $path) {
                $appendPath = $appendPath->append(blank($appendPath->__toString()) ? '' : '.', $path);

                $stringAppendPath = $appendPath->__toString();

                $schema = null;
                foreach ($allowedIncludesToMerge as $include) {
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
     * @throws RuntimeException
     */
    protected function resolve(string $path): Schema
    {
        $model = $this->model();

        foreach (explode('.', $path) as $path) {
            if (! method_exists($model, $path)) {
                $classBasename = $model::class;
                throw new RuntimeException("Relation '$path' does not exist on model '$classBasename'.");
            }
            $model = $model->$path()->getRelated();
        }

        if (method_exists($model, 'schema')) {
            return $model->schema();
        }

        $schema = Str::of($model::class)
            ->replace('Models', 'Scout\\Elasticsearch\\Api\\Schema')
            ->append('Schema')
            ->__toString();

        return new $schema;
    }

    public function model(): Model
    {
        $modelClass = Str::of(static::class)
            ->replace('Scout\\Elasticsearch\\Api\\Schema', 'Models')
            ->remove('Schema')
            ->__toString();

        return new $modelClass;
    }
}
