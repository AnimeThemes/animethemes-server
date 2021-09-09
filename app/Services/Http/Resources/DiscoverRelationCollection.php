<?php

declare(strict_types=1);

namespace App\Services\Http\Resources;

use App\Http\Api\Query;
use App\Http\Resources\BaseCollection;
use App\Services\DiscoverService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\Finder;

/**
 * Class DiscoverRelationCollection.
 */
class DiscoverRelationCollection extends DiscoverService
{
    /**
     * Get the resource collection class by model.
     *
     * @param  Model  $model
     * @return BaseCollection|null
     */
    public static function byModel(Model $model): ?BaseCollection
    {
        $resources = (new Finder())->files()->in(static::getPath());

        foreach ($resources as $resource) {
            try {
                $resource = new ReflectionClass(static::classFromFile($resource));
            } catch (ReflectionException $e) {
                Log::error($e->getMessage());
                continue;
            }

            if (! $resource->isInstantiable()) {
                continue;
            }

            if (! $resource->isSubclassOf(BaseCollection::class)) {
                continue;
            }

            if ($resource->hasProperty('collects')) {
                try {
                    $resourceInstance = $resource->newInstance(new MissingValue(), Query::make());
                    $collects = $resource->getProperty('collects')->getValue($resourceInstance);
                    if (get_class($model) === $collects) {
                        $baseCollectionInstance = $resource->newInstance(new MissingValue(), Query::make());
                        if ($baseCollectionInstance instanceof BaseCollection) {
                            return $baseCollectionInstance;
                        }
                    }
                } catch (ReflectionException $e) {
                    Log::error($e->getMessage());
                }
            }
        }

        return null;
    }

    /**
     * The path to the Eloquent API resources.
     *
     * @return string
     */
    protected static function getPath(): string
    {
        return app()->path(Str::of('Http')
            ->append(DIRECTORY_SEPARATOR)
            ->append('Resources')
            ->__toString());
    }
}
