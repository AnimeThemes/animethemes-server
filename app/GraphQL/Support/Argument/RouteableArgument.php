<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Argument;

use App\Contracts\GraphQL\Fields\RouteableField;
use App\GraphQL\Definition\Fields\Field;

class RouteableArgument extends Argument
{
    public function __construct(
        Field&RouteableField $field,
        bool $shouldRequire = true,
    ) {
        parent::__construct($field->getName(), $field->type());

        $this->required($shouldRequire);
    }
}
