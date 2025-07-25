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
use App\GraphQL\Definition\Directives\Filters\FilterDirective;
use App\GraphQL\Definition\Fields\Field;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait ResolvesArguments
{
    use ResolvesDirectives;

    /**
     * Build the arguments array into string.
     *
     * @param  array  $arguments
     */
    public function buildArguments(array $arguments): string
    {
        if (blank($arguments)) {
            return '';
        }

        return sprintf('(%s)', implode(', ', Arr::flatten($arguments)));
    }

    /**
     * Resolve the fields into arguments that are used for filtering.
     *
     * @param  Field[]  $fields
     * @return string[]
     */
    public function resolveFilterArguments(array $fields): array
    {
        return collect($fields)
            ->filter(fn (Field $field) => $field instanceof FilterableField)
            ->map(function (FilterableField $field) {
                return collect($field->filterDirectives())
                    ->map(fn (FilterDirective $directive) => $directive->__toString())
                    ->toArray();
            })
            ->flatten()
            ->toArray();
    }

    /**
     * Resolve the fields into arguments that are used for sorting.
     *
     * @param  Field[]  $fields
     * @return string[]
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
            Str::of('sort: [SortInput!] ')
                ->append($this->resolveDirectives([
                    'sortCustom' => [
                        'columns' => json_encode($columns),
                    ],
                ]))
                ->__toString(),
        ];
    }

    /**
     * Resolve the fields into arguments that are used for mutations of type create.
     *
     * @param  Field[]  $fields
     * @return string[]
     */
    public function resolveCreateMutationArguments(array $fields): array
    {
        return collect($fields)
            ->filter(fn (Field $field) => $field instanceof CreatableField)
            ->map(fn (Field $field) => sprintf(
                '%s: %s%s',
                $field->getColumn(),
                $field->type()->__toString(),
                $field instanceof RequiredOnCreation ? '!' : ''
            ))
            ->flatten()
            ->toArray();
    }

    /**
     * Resolve the fields into arguments that are used for mutations of type update.
     *
     * @param  Field[]  $fields
     * @return string[]
     */
    public function resolveUpdateMutationArguments(array $fields): array
    {
        return collect($fields)
            ->filter(fn (Field $field) => $field instanceof UpdatableField)
            ->map(fn (Field $field) => sprintf(
                '%s: %s%s',
                $field->getColumn(),
                $field->type()->__toString(),
                $field instanceof RequiredOnUpdate ? '!' : ''
            ))
            ->flatten()
            ->toArray();
    }

    /**
     * Resolve the bind argument.
     *
     * @param  array<int, Field>  $fields
     * @return string[]
     */
    public function resolveBindArgument(array $fields, bool $shouldRequire = true): array
    {
        return collect($fields)
            ->filter(fn (Field $field) => $field instanceof BindableField)
            ->map(fn (Field&BindableField $field) => sprintf(
                '%s: %s%s%s',
                $field->getName(),
                $field->type()->__toString(),
                $shouldRequire ? '!' : '',
                $this->resolveDirectives([
                    'bind' => [
                        'class' => $field->bindTo(),
                        'column' => $field->bindUsingColumn(),
                    ],
                ])
            ))
            ->toArray();
    }
}
