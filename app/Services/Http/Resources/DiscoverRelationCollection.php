<?php

declare(strict_types=1);

namespace App\Services\Http\Resources;

use App\Http\Api\QueryParser;
use App\Http\Resources\BaseCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * Class DiscoverRelationCollection.
 */
class DiscoverRelationCollection
{
    /**
     * Get the resource collection class by model.
     *
     * @param Model $model
     * @return string|null
     */
    public static function byModel(Model $model): ?string
    {
        $resources = (new Finder())->files()->in(static::resourcesPath());

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
                    $resourceInstance = $resource->newInstance(new MissingValue(), QueryParser::make());
                    $collects = $resource->getProperty('collects')->getValue($resourceInstance);
                    if (get_class($model) === $collects) {
                        return $resource->getName();
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
    protected static function resourcesPath(): string
    {
        return app()->path(Str::of('Http')
            ->append(DIRECTORY_SEPARATOR)
            ->append('Resources')
            ->__toString());
    }

    /**
     * Extract the class name from the given file path.
     *
     * @param  SplFileInfo  $file
     * @return string
     */
    protected static function classFromFile(SplFileInfo $file): string
    {
        $class = trim(Str::replaceFirst(base_path(), '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        return str_replace(
            [DIRECTORY_SEPARATOR, ucfirst(basename(app()->path())).'\\'],
            ['\\', app()->getNamespace()],
            ucfirst(Str::replaceLast('.php', '', $class))
        );
    }
}
