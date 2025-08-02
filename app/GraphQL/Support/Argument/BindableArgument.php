<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Argument;

use App\Contracts\GraphQL\Fields\BindableField;
use App\GraphQL\Definition\Fields\Field;

class BindableArgument extends Argument
{
    public function __construct(
        Field&BindableField $field,
        bool $shouldRequire = true,
    ) {
        parent::__construct($field->getName(), $field->type());

        $this->required($shouldRequire);

        $this->directives([
            'bind' => [
                'class' => $field->bindTo(),
                'column' => $field->bindUsingColumn(),
            ],
        ]);
    }
}
