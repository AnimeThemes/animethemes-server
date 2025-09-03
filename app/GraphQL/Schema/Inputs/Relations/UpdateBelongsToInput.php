<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Inputs\Relations;

use App\GraphQL\Schema\Inputs\Input;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Support\InputField;
use GraphQL\Type\Definition\Type;

class UpdateBelongsToInput extends Input
{
    public function __construct(
        protected EloquentType $type,
    ) {}

    public function getName(): string
    {
        return "Update{$this->type->getName()}BelongsTo";
    }

    /**
     * @return InputField[]
     */
    public function fieldClasses(): array
    {
        return [
            new InputField('connect', Type::int()),
            new InputField('create', "Create{$this->type->getName()}Input"),
            new InputField('update', "Update{$this->type->getName()}Input"),
            new InputField('disconnect', Type::boolean()),
            new InputField('delete', Type::boolean()),
        ];
    }
}
