<?php

declare(strict_types=1);

namespace App\GraphQL\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class UseFieldDirective
{
    /**
     * @param  class-string  $fieldClass
     */
    public function __construct(
        public string $fieldClass,
        public string $method = '__invoke',
    ) {}
}
