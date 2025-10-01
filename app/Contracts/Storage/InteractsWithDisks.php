<?php

declare(strict_types=1);

namespace App\Contracts\Storage;

interface InteractsWithDisks
{
    /**
     * The list of disk names.
     */
    public function disks(): array;
}
