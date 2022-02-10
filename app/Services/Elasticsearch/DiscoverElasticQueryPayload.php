<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch;

use App\Services\DiscoverService;
use App\Services\Elasticsearch\Api\Query\ElasticQueryPayload;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\Finder;

/**
 * Class DiscoverElasticQueryPayload.
 */
class DiscoverElasticQueryPayload extends DiscoverService
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
     * The path to the Elasticsearch query payload classes.
     *
     * @return string
     */
    protected static function getPath(): string
    {
        return app()->path(Str::of('Services')
            ->append(DIRECTORY_SEPARATOR)
            ->append('Elasticsearch')
            ->append(DIRECTORY_SEPARATOR)
            ->append('Api')
            ->append(DIRECTORY_SEPARATOR)
            ->append('Query')
            ->__toString());
    }
}
