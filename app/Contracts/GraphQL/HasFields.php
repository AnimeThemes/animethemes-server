<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL;

use App\GraphQL\Definition\Fields\Field;

/**
 * Interface HasFields.
 */
interface HasFields
{
    /**
     * The fields of the type.
     *
     * @return array<int, Field>
     */
    public function fields(): array;
}
