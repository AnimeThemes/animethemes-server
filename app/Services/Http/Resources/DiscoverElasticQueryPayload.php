<?php

declare(strict_types=1);

namespace App\Services\Http\Resources;

use App\Services\DiscoverService;
use App\Services\Models\Scout\ElasticQueryPayload;
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

            if ($payload->hasProperty('model')) {
                $model = $payload->getProperty('model')->getValue();
                if ($model === $modelClass) {
                    return $payload->getName();
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
            ->append('Models')
            ->append(DIRECTORY_SEPARATOR)
            ->append('Scout')
            ->__toString());
    }
}
