<?php

declare(strict_types=1);

namespace App\Concerns\GraphQL;

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
                ->append(implode("\n", Arr::flatten($arguments)))
                ->append(')')
                ->toString();
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
                        ->map(fn (FilterDirective $directive) => $directive->toString())
                        ->toArray();
                }
            })
            ->flatten()
            ->toArray();
    }
}
