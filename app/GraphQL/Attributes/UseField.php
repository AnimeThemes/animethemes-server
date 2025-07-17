<?php

declare(strict_types=1);

namespace App\GraphQL\Attributes;

use Attribute;

/**
 * Class UseBuilder.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class UseField
{
    /**
     * @param  class-string  $fieldClass
     * @param  string  $method
     */
    public function __construct(
        public string $fieldClass,
        public string $method,
    ) {}
}
