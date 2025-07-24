<?php

declare(strict_types=1);

namespace App\Contracts\Storage;

interface InteractsWithDisk
{
    /**
     * The name of the disk.
     */
    public function disk(): string;
}
