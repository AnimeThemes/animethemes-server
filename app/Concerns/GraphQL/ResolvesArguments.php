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
use App\Contracts\GraphQL\HasFields;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Directives\SortCustomDirective;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Directives\Filters\FilterDirective;
use App\GraphQL\Support\Sort\RandomSort;
use App\GraphQL\Support\SortableColumns;
use Illuminate\Support\Str;

trait ResolvesArguments
{
    use ResolvesDirectives;

    /**
     * Build the arguments array into string.
     *
     * @param  Argument[]  $arguments
     */
    protected function buildArguments(array $arguments): string
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
    protected function resolveFilterArguments(array $fields): array
    {
        return collect($fields)
            ->filter(fn (Field $field) => $field instanceof FilterableField)
            ->map(
                fn (FilterableField $field) => collect($field->filterDirectives())
                    ->map(fn (FilterDirective $directive) => $directive->argument())
                    ->toArray()
            )
            ->flatten()
            ->toArray();
    }

    /**
     * Resolve the fields into arguments that are used for sorting.
     *
     * @return Argument[]
     */
    protected function resolveSortArguments(BaseType&HasFields $type): array
    {
        $columns = collect($type->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (Field&SortableField $field) => [
                SortCustomDirective::INPUT_COLUMN => $field->getColumn(),
                SortCustomDirective::INPUT_VALUE => Str::of($field->getName())->snake()->upper()->__toString(),
                SortCustomDirective::INPUT_SORT_TYPE => $field->sortType()->value,
                SortCustomDirective::INPUT_RELATION => method_exists($field, 'relation') ? $field->{'relation'}() : null,
            ])
            // @phpstan-ignore-next-line
            ->push([
                SortCustomDirective::INPUT_VALUE => RandomSort::CASE,
            ])
            ->toArray();

        $suffix = SortableColumns::SUFFIX;

        return [
            new Argument('sort', "[{$type->getName()}{$suffix}!]")
                ->directives([
                    'sortCustom' => [
                        'columns' => json_encode($columns),
                    ],
                ]),
        ];
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
            ->filter(fn (Field $field) => $field instanceof CreatableField)
            ->map(
                fn (Field $field) => new Argument($field->getColumn(), $field->type())
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
            ->filter(fn (Field $field) => $field instanceof UpdatableField)
            ->map(
                fn (Field $field) => new Argument($field->getColumn(), $field->type())
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
    protected function resolveBindArguments(array $fields, bool $shouldRequire = true): array
    {
        return collect($fields)
            ->filter(fn (Field $field) => $field instanceof BindableField)
            ->map(
                fn (Field&BindableField $field) => new Argument($field->getName(), $field->type())
                    ->required($shouldRequire)
                    ->directives([
                        'bind' => [
                            'class' => $field->bindTo(),
                            'column' => $field->bindUsingColumn(),
                        ],
                    ])
            )
            ->toArray();
    }
}
