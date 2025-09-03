<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Inputs\Relations;

use App\GraphQL\Schema\Inputs\Input;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Support\InputField;
use GraphQL\Type\Definition\Type;

class UpdateHasManyInput extends Input
{
    public function __construct(
        protected EloquentType $type,
    ) {}

    public function getName(): string
    {
        return "Update{$this->type->getName()}HasMany";
    }

    /**
     * @return InputField[]
     */
    public function fieldClasses(): array
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
