<?php

declare(strict_types=1);

namespace App\Concerns\GraphQL;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\RequiredOnUpdate;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Argument\BindableArgument;

trait ResolvesArguments
{
    /**
     * Resolve the args.
     *
     * @return array<string, array<string, mixed>>
     */
    public function args(): array
    {
        return collect($this->arguments())
            ->mapWithKeys(fn (Argument $argument): array => [
                $argument->name => [
                    'name' => $argument->name,
                    'type' => $argument->getType(),

                    ...(is_null($argument->getDefaultValue()) ? [] : ['defaultValue' => $argument->getDefaultValue()]),
                ],
            ])
            ->toArray();
    }

    /**
     * Resolve the fields into arguments that are used for mutations of type create.
     *
     * @param  Field[]  $fields
     * @return Argument[]
     */
    protected function resolveCreateMutationArguments(array $fields): array
    {
        return collect($fields)
            ->filter(fn (Field $field): bool => $field instanceof CreatableField)
            ->map(
                fn (Field $field): Argument => new Argument($field->getColumn(), $field->baseType())
                    ->required($field instanceof RequiredOnCreation)
            )
            ->flatten()
            ->toArray();
    }

    /**
     * Resolve the fields into arguments that are used for mutations of type update.
     *
     * @param  Field[]  $fields
     * @return Argument[]
     */
    protected function resolveUpdateMutationArguments(array $fields): array
    {
        return collect($fields)
            ->filter(fn (Field $field): bool => $field instanceof UpdatableField)
            ->map(
                fn (Field $field): Argument => new Argument($field->getColumn(), $field->baseType())
                    ->required($field instanceof RequiredOnUpdate)
            )
            ->flatten()
            ->toArray();
    }

    /**
     * @param  Field[]  $fields
     * @return BindableArgument[]
     */
    protected function resolveBindArguments(array $fields, bool $shouldRequire = true): array
    {
        return collect($fields)
            ->filter(fn (Field $field): bool => $field instanceof BindableField)
            ->map(fn (Field&BindableField $field): BindableArgument => new BindableArgument($field, $shouldRequire))
            ->toArray();
    }
}
