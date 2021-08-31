<?php

declare(strict_types=1);

namespace App\Http\Api\Scope;

/**
 * Class GlobalScope.
 */
class GlobalScope extends Scope
{
    /**
     * Determine if the provided scope is within this scope.
     *
     * @param Scope $scope
     * @return bool
     */
    public function isWithinScope(Scope $scope): bool
    {
        return true;
    }
}
