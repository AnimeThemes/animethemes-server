<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Argument;

use GraphQL\Type\Definition\Type;

class SearchArgument extends Argument
{
    public function __construct()
    {
        parent::__construct('search', Type::string());
    }
}
