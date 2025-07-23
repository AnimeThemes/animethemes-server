<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

interface HasArgumentsField
{
    /**
     * Get the arguments of the field.
     *
     * @return array
     */
    public function arguments(): array;
}
