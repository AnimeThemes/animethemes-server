<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

/**
 * Interface DeletableField.
 */
interface DeletableField
{
    /**
     * Set the delete validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getDeleteRules(array $args): array;
}
