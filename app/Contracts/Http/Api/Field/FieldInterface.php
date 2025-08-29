<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

interface FieldInterface
{
    public function getKey(): string;
}
