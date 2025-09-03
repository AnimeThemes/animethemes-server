<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Inputs\Relations;

use App\GraphQL\Schema\Inputs\Input;
use App\GraphQL\Schema\Types\Pivot\PivotType;
use App\GraphQL\Support\InputField;

class CreateBelongsToManyInput extends Input
{
    public function __construct(
        protected PivotType $type,
    ) {}

    public function getName(): string
    {
        return "Create{$this->type->getName()}BelongsToMany";
    }

    /**
     * @return InputField[]
     */
    public function fieldClasses(): array
    {
        return [
            new InputField('create', "[Create{$this->type->getName()}Input!]"),
            // new InputField('connect', "[Connect{$this->type->getName()}Input!]"),
        ];
    }
}
