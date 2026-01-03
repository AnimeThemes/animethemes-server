<?php

declare(strict_types=1);

namespace App\GraphQL\Argument;

use GraphQL\Type\Definition\Type;

class PageArgument extends Argument
{
    public function __construct()
    {
        parent::__construct('page', Type::int());
    }
}
