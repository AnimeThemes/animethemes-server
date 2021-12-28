<?php

declare(strict_types=1);

namespace App\Http\Api\Scope;

use Illuminate\Support\Str;

/**
 * CLass RelationScope.
 */
class RelationScope extends Scope
{
    /**
     * Create a new scope instance.
     *
     * @param  string  $relation
     */
    public function __construct(protected string $relation) {}

    /**
     * Get the relation of the scope.
     *
     * @return string
     */
    public function getRelation(): string
    {
        return $this->relation;
    }

    /**
     * Get the type of the relation.
     *
     * @return string
     */
    public function getType(): string
    {
        return Str::of($this->getRelation())->explode('.')->last();
    }

    /**
     * Determine if the provided scope is within this scope.
     *
     * @param  Scope  $scope
     * @return bool
     */
    public function isWithinScope(Scope $scope): bool
    {
        return $scope instanceof RelationScope && $this->relation === $scope->getRelation();
    }
}
