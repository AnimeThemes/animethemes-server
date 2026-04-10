<?php

declare(strict_types=1);

namespace App\Http\Api\Schema;

use App\Contracts\Http\Api\InteractsWithSchema;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Query\Query;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseJsonResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RuntimeException;

abstract class EloquentSchema extends Schema
{
    /** @var array<string, Schema> */
    protected array $resolveCache = [];

    /**
     * Get the resource of the schema.
     */
    public function resource(mixed $resource, Query $query): BaseJsonResource
    {
        $resourceClass = Str::of(static::class)
            ->replace('Api\\Schema', 'Resources')
            ->replace('Schema', 'JsonResource')
            ->replaceLast('\\', '\\Resource\\')
            ->__toString();

        return new $resourceClass($resource, $query);
    }

    /**
     * Get the collection of the schema.
     */
    public function collection(mixed $resource, Query $query): BaseCollection
    {
        $collectionClass = Str::of(static::class)
            ->replace('Api\\Schema', 'Resources')
            ->replace('Schema', 'Collection')
            ->replaceLast('\\', '\\Collection\\')
            ->__toString();

        return new $collectionClass($resource, $query);
    }

    public function model(): Model
    {
        $modelClass = Str::of(static::class)
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
        $allowedIncludes = [];
        $schemasCache = [];

        $includesByPath = [];
        foreach ($allowedIncludesToMerge as $include) {
            $includesByPath[$include->path()] = $include;
        }

        foreach ($allowedIncludesToMerge as $allowedInclude) {
            if ($allowedInclude->isDirectRelation()) {
                $allowedIncludes[$allowedInclude->path()] = $allowedInclude;
                continue;
            }

            $parts = explode('.', $allowedInclude->path());
            $currentPath = '';

            foreach ($parts as $part) {
                $currentPath = $currentPath === '' ? $part : "$currentPath.$part";

                if (isset($allowedIncludes[$currentPath])) {
                    continue;
                }

                if (isset($includesByPath[$currentPath])) {
                    $schema = $includesByPath[$currentPath]->schema();
                } else {
                    if (! isset($schemasCache[$currentPath])) {
                        $schemasCache[$currentPath] = $this->resolve($currentPath);
                    }

                    $schema = $schemasCache[$currentPath];
                }

                $allowedIncludes[$currentPath] = new AllowedInclude($schema, $currentPath);
            }
        }

        return array_values($allowedIncludes);
    }

    protected function resolve(string $path): Schema
    {
        if (isset($this->resolveCache[$path])) {
            return $this->resolveCache[$path];
        }

        $model = $this->model();

        foreach (explode('.', $path) as $segment) {
            throw_unless(method_exists($model, $segment), RuntimeException::class, "Relation '$segment' does not exist on model '".$model::class."'.");

            $model = $model->$segment()->getRelated();
        }

        $schema = $model instanceof InteractsWithSchema
            ? $model->schema()
            : new (str_replace('Models', 'Http\\Api\\Schema', $model::class).'Schema');

        return $this->resolveCache[$path] = $schema;
    }
}
