<?php

declare(strict_types=1);

namespace App\GraphQL\Types\Definition;

use Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class Hidden
{
    public function __construct(
        public bool $hidden = false,
    ) {}
}
