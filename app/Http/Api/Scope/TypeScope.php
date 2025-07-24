<?php

declare(strict_types=1);

namespace App\Http\Api\Scope;

class TypeScope extends Scope
{
    public function __construct(protected readonly string $type) {}

    /**
     * Get the type of the scope.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Determine if the provided scope is within this scope.
     */
    public function isWithinScope(Scope $scope): bool
    {
        if ($scope instanceof TypeScope) {
            return $this->type === $scope->getType();
        }

        return $scope instanceof RelationScope && $this->type === $scope->getType();
    }
}
