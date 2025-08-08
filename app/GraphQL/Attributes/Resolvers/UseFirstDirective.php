<?php

declare(strict_types=1);

namespace App\GraphQL\Attributes\Resolvers;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class UseFirstDirective
{
    public function __construct(
        public bool $shouldUse = true,
    ) {}
}
