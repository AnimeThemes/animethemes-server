<?php

declare(strict_types=1);

namespace App\Http\Api\Scope;

/**
 * CLass TypeScope.
 */
class TypeScope extends Scope
{
    /**
     * Create a new scope instance.
     *
     * @param  string  $type
     */
    public function __construct(protected string $type) {}

    /**
     * Get the type of the scope.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Determine if the provided scope is within this scope.
     *
     * @param  Scope  $scope
     * @return bool
     */
    public function isWithinScope(Scope $scope): bool
    {
        if ($scope instanceof TypeScope) {
            return $this->type === $scope->getType();
        }

        return $scope instanceof RelationScope && $this->type === $scope->getType();
    }
}
