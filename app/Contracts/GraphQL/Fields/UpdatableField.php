<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

/**
 * Interface UpdatableField.
 */
interface UpdatableField
{
    /**
     * Set the update validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getUpdateRules(array $args): array;
}
