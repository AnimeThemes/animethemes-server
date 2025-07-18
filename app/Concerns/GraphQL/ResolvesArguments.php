<?php

declare(strict_types=1);

namespace App\Concerns\GraphQL;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\Contracts\GraphQL\FilterableField;
use App\GraphQL\Definition\Directives\Filters\FilterDirective;
use App\GraphQL\Definition\Fields\Field;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Trait ResolvesArguments.
 */
trait ResolvesArguments
{
    use ResolvesDirectives;

    /**
     * Build the arguments array into string.
     *
     * @param  array  $arguments
     * @return string
     */
    public function buildArguments(array $arguments): string
    {
        if (filled($arguments)) {
            return Str::of('(')
                ->append(implode(PHP_EOL, Arr::flatten($arguments)))
                ->append(')')
                ->__toString();
        }

        return '';
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
            ->map(function (Field $field) {
                if ($field instanceof FilterableField) {
                    return collect($field->filterDirectives())
                        ->map(fn (FilterDirective $directive) => $directive->__toString())
                        ->toArray();
                }
            })
            ->flatten()
            ->toArray();
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
            ->map(function (Field&CreatableField $field) {
                $rules = $field->getCreationRules([]);
                $required = in_array('required', $rules) && ! in_array('sometimes', $rules);

                return Str::of($field->getColumn())
                    ->append(': ')
                    ->append($field->type()->__toString())
                    ->append($required ? '!' : '')
                    ->__toString();
            })
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
            ->map(function (Field&UpdatableField $field) {
                $rules = $field->getUpdateRules([]);
                $required = in_array('required', $rules) && ! in_array('sometimes', $rules);

                return Str::of($field->getColumn())
                    ->append(': ')
                    ->append($field->type()->__toString())
                    ->append($required ? '!' : '')
                    ->__toString();
            })
            ->flatten()
            ->toArray();
    }

    /**
     * Resolve the bind argument.
     *
     * @param  array<int, Field>  $fields
     * @return string[]
     */
    public function resolveBindArgument(array $fields): array
    {
        return collect($fields)
            ->filter(fn (Field $field) => $field instanceof BindableField)
            ->map(function (Field $field) {
                return Str::of($field->getName())
                    ->append(': ')
                    ->append($field->type()->__toString())
                    ->append('! ')
                    ->append($this->getBindDirective($field))
                    ->__toString();
            })
            ->toArray();
    }

    private function getBindDirective(Field&BindableField $field): string
    {
        return $this->resolveDirectives([
            'bind' => [
                'class' => $field->bindTo(),
                'column' => $field->bindUsingColumn(),
            ],
        ]);
    }
}
