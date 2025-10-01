<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

interface UpdatableField
{
    /**
     * @param  array<string, mixed>  $args
     */
    public function getUpdateRules(array $args): array;
}
