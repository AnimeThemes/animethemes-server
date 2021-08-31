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
     * The relation of the scope.
     *
     * @var string
     */
    protected string $relation;

    /**
     * Create a new scope instance.
     *
     * @param string $relation
     */
    public function __construct(string $relation)
    {
        $this->relation = $relation;
    }

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
     * @param Scope $scope
     * @return bool
     */
    public function isWithinScope(Scope $scope): bool
    {
        if ($scope instanceof RelationScope) {
            return $this->relation === $scope->getRelation();
        }

        return false;
    }
}
