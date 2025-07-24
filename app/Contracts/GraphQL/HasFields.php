<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL;

use App\GraphQL\Definition\Fields\Field;

interface HasFields
{
    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fields(): array;
}
