<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use GraphQL\Type\Definition\Type;

/**
 * Class StringField.
 */
abstract class StringField extends Field
{
    /**
     * The type returned by the field.
     *
     * @return Type
     */
    protected function type(): Type
    {
        return Type::string();
    }
}
