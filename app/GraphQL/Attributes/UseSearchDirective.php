<?php

declare(strict_types=1);

namespace App\GraphQL\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class UseSearchDirective {}
