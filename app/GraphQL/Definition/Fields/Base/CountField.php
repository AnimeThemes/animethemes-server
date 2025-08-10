<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Base;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Field;
use GraphQL\Type\Definition\Type;

class CountField extends Field implements DisplayableField
{
    public function __construct(
        protected string $relation,
        protected string $column,
        protected ?string $name = null,
        protected bool $nullable = false,
    ) {
        parent::__construct($column, $name, $nullable);
    }

    /**
     * The type returned by the field.
     */
    public function baseType(): Type
    {
        return Type::int();
    }

    /**
     * Determine if the field should be displayed to the user.
     */
    public function canBeDisplayed(): bool
    {
        return true;
    }
}
