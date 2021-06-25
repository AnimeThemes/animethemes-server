<?php

declare(strict_types=1);

namespace App\Services\Http\Resources;

use App\Services\Models\Scout\ElasticQueryPayload;
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
     * @param string $modelClass
     * @return string|null
     */
    public static function byModelClass(string $modelClass): ?string
    {
        $payloads = (new Finder())->files()->in(static::payloadPath());

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
    protected static function payloadPath(): string
    {
        return app()->path(Str::of('Services')
            ->append(DIRECTORY_SEPARATOR)
            ->append('Models')
            ->append(DIRECTORY_SEPARATOR)
            ->append('Scout')
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
