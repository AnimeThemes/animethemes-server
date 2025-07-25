<?php

declare(strict_types=1);

namespace App\Concerns\GraphQL;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\RequiredOnUpdate;
use App\Contracts\GraphQL\Fields\SortableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Argument\Argument;
use App\GraphQL\Definition\Directives\Filters\FilterDirective;
use App\GraphQL\Definition\Fields\Field;

trait ResolvesArguments
{
    use ResolvesDirectives;

    /**
     * Build the arguments array into string.
     *
     * @param  Argument[]  $arguments
     */
    public function buildArguments(array $arguments): string
    {
        if (blank($arguments)) {
            return '';
        }

        $arguments = collect($arguments)
            ->flatten()
            ->map(fn (Argument $argument) => $argument->__toString())
            ->implode(', ');

        return sprintf('(%s)', $arguments);
    }

    /**
     * Resolve the fields into arguments that are used for filtering.
     *
     * @param  Field[]  $fields
     * @return Argument[]
     */
    public function resolveFilterArguments(array $fields): array
    {
        return collect($fields)
            ->filter(fn (Field $field) => $field instanceof FilterableField)
            ->map(function (FilterableField $field) {
                return collect($field->filterDirectives())
                    ->map(fn (FilterDirective $directive) => $directive->argument())
                    ->toArray();
            })
            ->flatten()
            ->toArray();
    }

    /**
     * Resolve the fields into arguments that are used for sorting.
     *
     * @param  Field[]  $fields
     * @return Argument[]
     */
    public function resolveSortArguments(array $fields): array
    {
        $columns = collect($fields)
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (Field&SortableField $field) => [
                'column' => $field->getColumn(),
                'sortType' => $field->sortType()->value,
                'relation' => method_exists($field, 'relation') ? $field->{'relation'}() : null,
            ])
            ->toArray();

        return [
            new Argument(
                'sort',
                '[SortInput!]',
                [
                    'sortCustom' => [
                        'columns' => json_encode($columns),
                    ],
                ],
            ),
        ];
    }

    /**
     * Resolve the fields into arguments that are used for mutations of type create.
     *
     * @param  Field[]  $fields
     * @return Argument[]
     */
    public function resolveCreateMutationArguments(array $fields): array
    {
        return collect($fields)
            ->filter(fn (Field $field) => $field instanceof CreatableField)
            ->map(fn (Field $field) =>
                new Argument(
                    $field->getColumn(),
                    $field->type()
                )
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
    public function resolveUpdateMutationArguments(array $fields): array
    {
        return collect($fields)
            ->filter(fn (Field $field) => $field instanceof UpdatableField)
            ->map(fn (Field $field) =>
                new Argument(
                    $field->getColumn(),
                    $field->type()
                )
                ->required($field instanceof RequiredOnUpdate)
            )
            ->flatten()
            ->toArray();
    }

    /**
     * Resolve the bind arguments.
     *
     * @param  Field[]  $fields
     * @return Argument[]
     */
    public function resolveBindArguments(array $fields, bool $shouldRequire = true): array
    {
        return collect($fields)
            ->filter(fn (Field $field) => $field instanceof BindableField)
            ->map(fn (Field&BindableField $field) =>
                new Argument(
                    $field->getName(),
                    $field->type(),
                    [
                        'bind' => [
                            'class' => $field->bindTo(),
                            'column' => $field->bindUsingColumn(),
                        ],
                    ],
                )
                ->required($shouldRequire)
            )
            ->toArray();
    }
}
