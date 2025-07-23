<?php

declare(strict_types=1);

namespace App\Concerns\GraphQL;

trait ResolvesDirectives
{
    /**
     * Convert the array of directives into a string format for GraphQL.
     *
     * @param  array<string, array<string, mixed>>  $directives
     */
    public function resolveDirectives(array $directives): string
    {
        return collect($directives)
            ->map(function ($args, $directive) {
                if (blank($args)) {
                    return sprintf('@%s', $directive);
                }

                $argsString = collect($args)
                    ->map(fn ($value, $key) => sprintf('%s: %s', $key, json_encode($value)))
                    ->implode(', ');

                return sprintf('@%s(%s)', $directive, $argsString);
            })
            ->implode(' ');
    }
}
