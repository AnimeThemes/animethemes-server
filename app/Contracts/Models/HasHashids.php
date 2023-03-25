<?php

declare(strict_types=1);

namespace App\Contracts\Models;

/**
 * Interface HasHashids.
 *
 * @property string $hashid
 */
interface HasHashids
{
    final public const ATTRIBUTE_HASHID = 'hashid';

    /**
     * Get the numbers used to encode the model's hashids.
     *
     * @return array<int, int>
     */
    public function hashids(): array;
}
