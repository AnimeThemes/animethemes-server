<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Input;

use App\GraphQL\Support\InputField;
use Illuminate\Support\Arr;
use Stringable;

abstract class Input implements Stringable
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

    /**
     * Get the input as a string representation.
     */
    public function __toString(): string
    {
        if (blank($this->fields())) {
            return '';
        }

        return sprintf(
            'input %s {
                %s
            }',
            $this->getName(),
            implode(PHP_EOL, Arr::map($this->fields(), fn (InputField $field) => $field->__toString()))
        );
    }
}
