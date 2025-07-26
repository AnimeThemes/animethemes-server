<?php

declare(strict_types=1);

namespace App\GraphQL\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class Deprecated
{
    public function __construct(
        public string $reason,
    ) {}
}
