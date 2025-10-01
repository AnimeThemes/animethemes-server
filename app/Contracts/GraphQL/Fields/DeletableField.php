<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

interface DeletableField
{
    /**
     * @param  array<string, mixed>  $args
     */
    public function getDeleteRules(array $args): array;
}
