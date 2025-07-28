<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Input\Relations;

use App\GraphQL\Definition\Input\Input;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Support\InputField;
use GraphQL\Type\Definition\Type;

class CreateHasManyInput extends Input
{
    public function __construct(
        protected EloquentType $type,
    ) {
        parent::__construct("Create{$type->getName()}HasMany");
    }

    /**
     * The input fields.
     *
     * @return InputField[]
     */
    public function fields(): array
    {
        return [
            new InputField('create', "[Create{$this->type->getName()}Input!]"),
            new InputField('update', "[Update{$this->type->getName()}Input!]"),
            new InputField('connect', Type::listof(Type::nonNull(Type::int()))),
            new InputField('disconnect', Type::listof(Type::nonNull(Type::int()))),
            new InputField('delete', Type::listof(Type::nonNull(Type::int()))),
        ];
    }
}
