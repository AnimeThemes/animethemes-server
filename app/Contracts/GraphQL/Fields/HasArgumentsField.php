<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

use App\GraphQL\Support\Argument;

interface HasArgumentsField
{
    /**
     * Get the arguments of the field.
     *
     * @return Argument[]
     */
    public function arguments(): array;
}
