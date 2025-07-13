<?php

declare(strict_types=1);

namespace App\GraphQL\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class Hidden
{
    public function __construct(
        public bool $hidden = true,
    ) {}
}
