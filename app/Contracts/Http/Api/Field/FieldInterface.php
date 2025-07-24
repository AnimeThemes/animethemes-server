<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

interface FieldInterface
{
    /**
     * Get the field key.
     */
    public function getKey(): string;
}
