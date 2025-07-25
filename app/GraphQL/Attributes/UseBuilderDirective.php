<?php

declare(strict_types=1);

namespace App\GraphQL\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class UseBuilderDirective
{
    /**
     * @param  class-string  $builderClass
     */
    public function __construct(
        public string $builderClass,
        public string $method = 'index',
    ) {}
}
