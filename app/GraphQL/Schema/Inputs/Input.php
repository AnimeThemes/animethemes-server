<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Inputs;

use App\GraphQL\Support\InputField;
use Rebing\GraphQL\Support\InputType;

abstract class Input extends InputType
{
    public function getAttributes(): array
    {
        return [
            'name' => $this->getName(),
        ];
    }

    public function getName(): string
    {
        return class_basename($this);
    }

    /**
     * @return InputField[]
     */
    public function fieldClasses(): array
    {
        return [];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function fields(): array
    {
        return collect($this->fieldClasses())
            ->mapWithKeys(fn (InputField $field): array => [
                $field->getName() => [
                    'name' => $field->getName(),
                    'type' => $field->getType(),
                    'rules' => $field->getRules(),
                ],
            ])
            ->toArray();
    }
}
