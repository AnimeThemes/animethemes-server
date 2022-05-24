<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

/**
 * Interface FieldInterface.
 */
interface FieldInterface
{
    /**
     * Get the field key.
     *
     * @return string
     */
    public function getKey(): string;
}
