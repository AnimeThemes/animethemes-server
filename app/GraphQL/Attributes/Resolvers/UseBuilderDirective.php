<?php

declare(strict_types=1);

namespace App\GraphQL\Attributes\Resolvers;

use App\GraphQL\Controllers\BaseController;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class UseBuilderDirective
{
    /**
     * @param  class-string<BaseController>  $controllerClass
     */
    public function __construct(
        public string $controllerClass,
        public string $method = 'index',
    ) {}
}
