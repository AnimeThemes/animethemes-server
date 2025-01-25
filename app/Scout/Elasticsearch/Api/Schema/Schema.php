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
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;
use Illuminate\Support\Str;

/**
 * Class Schema.
 */
abstract class Schema implements SchemaInterface
{
    /**
     * The model this schema represents.
     *
     * @return ElasticQuery
     */
    abstract public function query(): ElasticQuery;

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
                $allowedIncludes->put($allowedInclude->path(), new AllowedInclude($allowedInclude->schema(), $allowedInclude->path()));
                continue;
            }

            $appendPath = Str::of('');
            foreach (explode('.', $allowedInclude->path()) as $path) {
                $appendPath = $appendPath->append(empty($appendPath->__toString()) ? '' : '.', $path);

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
     * Resolve the schema by path.
     *
     * @param  string  $path
     * @return Schema
     *
     * @throws RuntimeException
     */
    protected function resolve(string $path): Schema
    {
        $model = $this->model();

        foreach (explode('.', $path) as $path) {
            if (!method_exists($model, $path)) {
                $classBasename = get_class($model);
                throw new RuntimeException("Relation '$path' does not exist on model '$classBasename'.");
            }
            $model = $model->$path()->getRelated();
        }

        if (method_exists($model, 'schema')) {
            return $model->schema();
        }

        $schema = Str::of(get_class($model))
            ->replace('Models', 'Scout\\Elasticsearch\\Api\\Schema')
            ->append('Schema')
            ->__toString();

        return new $schema;
    }

    /**
     * Resolve the owner model of the schema.
     *
     * @return Model
     */
    public function model(): Model
    {
        $modelClass = Str::of(get_class($this))
            ->replace('Scout\\Elasticsearch\\Api\\Schema', 'Models')
            ->remove('Schema')
            ->__toString();

        return new $modelClass;
    }
}
