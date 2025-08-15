<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Input;

use App\GraphQL\Support\InputField;

abstract class Input
{
    public function __construct(
        protected string $name,
    ) {}

    /**
     * Get the name of the input.
     * Input name appends 'Input'.
     */
    public function getName(): string
    {
        return $this->name.'Input';
    }

    /**
     * The input fields.
     *
     * @return InputField[]
     */
    abstract public function fields(): array;
}
