<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Input\Relations;

use App\GraphQL\Definition\Input\Input;
use App\GraphQL\Definition\Types\Pivot\PivotType;
use App\GraphQL\Support\InputField;

class CreateBelongsToManyInput extends Input
{
    public function __construct(
        protected PivotType $type,
    ) {
        parent::__construct("Create{$type->getName()}BelongsToMany");
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
            // new InputField('connect', "[Connect{$this->type->getName()}Input!]"),
        ];
    }
}
