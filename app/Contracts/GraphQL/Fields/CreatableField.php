<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

/**
 * Interface CreatableField.
 */
interface CreatableField
{
    /**
     * Set the creation validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array;
}
