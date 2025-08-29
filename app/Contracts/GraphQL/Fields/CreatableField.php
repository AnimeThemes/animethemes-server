<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

interface CreatableField
{
    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array;
}
