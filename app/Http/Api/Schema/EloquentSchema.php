<?php

declare(strict_types=1);

namespace App\Http\Api\Schema;

use App\Contracts\Http\Api\InteractsWithSchema;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Query\Query;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RuntimeException;

abstract class EloquentSchema extends Schema
{
    /**
     * Get the resource of the schema.
     */
    public function resource(mixed $resource, Query $query): BaseResource
    {
        $resourceClass = Str::of(get_class($this))
            ->replace('Api\\Schema', 'Resources')
            ->replace('Schema', 'Resource')
            ->replaceLast('\\', '\\Resource\\')
            ->__toString();

        return new $resourceClass($resource, $query);
    }

    /**
     * Get the collection of the schema.
     */
    public function collection(mixed $resource, Query $query): BaseCollection
    {
        $collectionClass = Str::of(get_class($this))
            ->replace('Api\\Schema', 'Resources')
            ->replace('Schema', 'Collection')
            ->replaceLast('\\', '\\Collection\\')
            ->__toString();

        return new $collectionClass($resource, $query);
    }

    /**
     * Resolve the owner model of the schema.
     */
    public function model(): Model
    {
        $modelClass = Str::of(get_class($this))
            ->replace('Http\\Api\\Schema', 'Models')
            ->remove('Schema')
            ->__toString();

        return new $modelClass;
    }

    /**
     * Merge the allowed includes with intermediate paths.
     *
     * @param  AllowedInclude[]  $allowedIncludesToMerge
     * @return AllowedInclude[]
     */
    protected function withIntermediatePaths(array $allowedIncludesToMerge = []): array
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
     * @throws RuntimeException
     */
    protected function resolve(string $path): Schema
    {
        $model = $this->model();

        foreach (explode('.', $path) as $path) {
            if (! method_exists($model, $path)) {
                $classBasename = get_class($model);
                throw new RuntimeException("Relation '$path' does not exist on model '$classBasename'.");
            }
            $model = $model->$path()->getRelated();
        }

        if ($model instanceof InteractsWithSchema) {
            return $model->schema();
        }

        $schema = Str::of(get_class($model))
            ->replace('Models', 'Http\\Api\\Schema')
            ->append('Schema')
            ->__toString();

        return new $schema;
    }
}
