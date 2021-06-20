<?php

declare(strict_types=1);

namespace App\Contracts\Models;

/**
 * Interface Nameable.
 */
interface Nameable
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string;
}
