<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Interface Nameable
 * @package App\Contracts
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
