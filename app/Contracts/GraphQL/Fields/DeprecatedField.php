<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

interface DeprecatedField
{
    /**
     * The reason which the field is deprecated.
     */
    public function deprecationReason(): string;
}
