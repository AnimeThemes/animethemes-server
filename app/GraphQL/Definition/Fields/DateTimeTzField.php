<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use GraphQL\Type\Definition\Type;
use Nuwave\Lighthouse\Schema\TypeRegistry;

/**
 * Class DateTimeTzField.
 */
abstract class DateTimeTzField extends Field
{
    /**
     * The type returned by the field.
     *
     * @return Type
     */
    protected function type(): Type
    {
        return app(TypeRegistry::class)->get('DateTimeTz');
    }
}
