<?php

declare(strict_types=1);

namespace App\GraphQL\Argument;

use App\Contracts\GraphQL\Fields\BindableField;
use App\GraphQL\Schema\Fields\Field;

class BindableArgument extends Argument
{
    public function __construct(
        Field&BindableField $field,
        bool $shouldRequire = true,
    ) {
        parent::__construct($field->getName(), $field->baseType());

        $this->required($shouldRequire);
    }
}
