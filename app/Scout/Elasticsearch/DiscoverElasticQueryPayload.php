<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch;

use App\Scout\Elasticsearch\Api\Query\ElasticQueryPayload;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * Class DiscoverElasticQueryPayload.
 */
class DiscoverElasticQueryPayload
{
    /**
     * Gets the Elasticsearch query payload class by model class.
     *
     * @param  string  $modelClass
     * @return string|null
     */
    public static function byModelClass(string $modelClass): ?string
    {
        $payloads = (new Finder())->files()->in(static::getPath());

        foreach ($payloads as $payload) {
            try {
                $payload = new ReflectionClass(static::classFromFile($payload));
            } catch (ReflectionException $e) {
                Log::error($e->getMessage());
                continue;
            }

            if (! $payload->isInstantiable()) {
                continue;
            }

            if (! $payload->isSubclassOf(ElasticQueryPayload::class)) {
                continue;
            }

            if ($payload->hasMethod('model')) {
                try {
                    $method = $payload->getMethod('model');
                    if ($modelClass === $method->invoke(null)) {
                        return $payload->getName();
                    }
                } catch (ReflectionException $e) {
                    Log::error($e->getMessage());
                    continue;
                }
            }
        }

        return null;
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

    /**
     * The path to the Elasticsearch query payload classes.
     *
     * @return string
     */
    protected static function getPath(): string
    {
        return app()->path(Str::of('Scout')
            ->append(DIRECTORY_SEPARATOR)
            ->append('Elasticsearch')
            ->append(DIRECTORY_SEPARATOR)
            ->append('Api')
            ->append(DIRECTORY_SEPARATOR)
            ->append('Query')
            ->__toString());
    }
}
