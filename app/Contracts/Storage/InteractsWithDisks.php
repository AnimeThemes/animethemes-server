<?php

declare(strict_types=1);

namespace App\Contracts\Storage;

/**
 * Class InteractsWithDisks.
 */
interface InteractsWithDisks
{
    /**
     * The list of disk names.
     *
     * @return array
     */
    public function disks(): array;
}
