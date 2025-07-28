<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Input\Relations;

use App\GraphQL\Definition\Input\Input;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Support\InputField;
use GraphQL\Type\Definition\Type;

class UpdateBelongsToInput extends Input
{
    public function __construct(
        protected EloquentType $type,
    ) {
        parent::__construct("Update{$type->getName()}BelongsTo");
    }

    /**
     * The input fields.
     *
     * @return InputField[]
     */
    public function fields(): array
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
