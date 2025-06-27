<?php

declare(strict_types=1);

namespace App\Http\Api\Scope;

use Illuminate\Support\Str;

/**
 * Class ScopeParser.
 */
class ScopeParser
{
    /**
     * Parse scope instance from string.
     *
     * @param  string  $scope
     * @return Scope
     */
    public static function parse(string $scope): Scope
    {
        if (empty($scope)) {
            return new GlobalScope();
        }

        if (Str::contains($scope, '.')) {
            return new RelationScope(
                Str::of($scope)
                    ->explode('.')
                    ->map(fn (string $scopePart) => Str::singular($scopePart))
                    ->join('.')
            );
        }

        return new TypeScope(Str::singular($scope));
    }
}
