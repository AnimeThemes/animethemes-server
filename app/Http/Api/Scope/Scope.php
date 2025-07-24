<?php

declare(strict_types=1);

namespace App\Http\Api\Scope;

abstract class Scope
{
    /**
     * Determine if the provided scope is within this scope.
     */
    abstract public function isWithinScope(Scope $scope): bool;
}
