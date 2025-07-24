<?php

declare(strict_types=1);

namespace App\Http\Api\Scope;

class GlobalScope extends Scope
{
    /**
     * Determine if the provided scope is within this scope.
     */
    public function isWithinScope(Scope $scope): bool
    {
        return true;
    }
}
