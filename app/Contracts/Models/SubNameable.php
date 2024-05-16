<?php

declare(strict_types=1);

namespace App\Contracts\Models;

/**
 * Interface SubNameable.
 */
interface SubNameable
{
    /**
     * Get subname.
     *
     * @return string
     */
    public function getSubName(): string;
}
