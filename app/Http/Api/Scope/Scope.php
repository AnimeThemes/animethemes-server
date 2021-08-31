<?php

declare(strict_types=1);

namespace App\Http\Api\Scope;

/**
 * Class Scope.
 */
abstract class Scope
{
    /**
     * Determine if the provided scope is within this scope.
     *
     * @param Scope $scope
     * @return bool
     */
    abstract public function isWithinScope(Scope $scope): bool;
}
