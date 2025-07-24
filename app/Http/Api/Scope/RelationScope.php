<?php

declare(strict_types=1);

namespace App\Http\Api\Scope;

use Illuminate\Support\Str;

class RelationScope extends Scope
{
    public function __construct(protected readonly string $relation) {}

    /**
     * Get the relation of the scope.
     */
    public function getRelation(): string
    {
        return $this->relation;
    }

    /**
     * Get the type of the relation.
     */
    public function getType(): string
    {
        return Str::of($this->getRelation())->explode('.')->last();
    }

    /**
     * Determine if the provided scope is within this scope.
     */
    public function isWithinScope(Scope $scope): bool
    {
        return $scope instanceof RelationScope && $this->relation === $scope->getRelation();
    }
}
