<?php

declare(strict_types=1);

namespace App\GraphQL\Attributes;

use Attribute;

/**
 * Class Hidden.
 */
#[Attribute(Attribute::TARGET_ALL)]
class Hidden
{
    /**
     * @param  bool  $hidden
     */
    public function __construct(
        public bool $hidden = true,
    ) {}
}
