<?php

declare(strict_types=1);

namespace App\Contracts\Models;

interface Nameable
{
    /**
     * Get name.
     */
    public function getName(): string;
}
