<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;
use SplFileInfo;

/**
 * Class DiscoverService.
 */
abstract class DiscoverService
{
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
     * The path to the classes to be discovered.
     *
     * @return string
     */
    abstract protected static function getPath(): string;
}
