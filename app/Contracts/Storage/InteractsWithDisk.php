<?php

declare(strict_types=1);

namespace App\Contracts\Storage;

/**
 * Class InteractsWithDisk.
 */
interface InteractsWithDisk
{
    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string;
}
