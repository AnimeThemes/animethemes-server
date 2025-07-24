<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Scope;

use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\RelationScope;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TypeScopeTest extends TestCase
{
    use WithFaker;

    /**
     * A global scope shall not be within the scope of a type scope.
     */
    public function testGlobalScopeIsNotWithinScope(): void
    {
        $scope = new TypeScope($this->faker->word());

        $otherScope = new GlobalScope();

        static::assertFalse($scope->isWithinScope($otherScope));
    }

    /**
     * A type scope of unequal value shall not be within the scope of a type scope.
     */
    public function testTypeScopeIsNotWithinScope(): void
    {
        $scope = new TypeScope($this->faker->word());

        $otherScope = new TypeScope($this->faker->word());

        static::assertFalse($scope->isWithinScope($otherScope));
    }

    /**
     * A type scope of equal value shall be within the scope of a type scope.
     */
    public function testTypeScopeIsWithinScope(): void
    {
        $type = $this->faker->word();

        $scope = new TypeScope($type);

        $otherScope = new TypeScope($type);

        static::assertTrue($scope->isWithinScope($otherScope));
    }

    /**
     * A relation scope of unequal type shall not be within the scope of a type scope.
     */
    public function testRelationScopeIsNotWithinScope(): void
    {
        $scope = new TypeScope($this->faker->word());

        $otherScope = new RelationScope($this->faker->word());

        static::assertFalse($scope->isWithinScope($otherScope));
    }

    /**
     * A relation scope of equal value shall be within the scope of a type scope.
     */
    public function testRelationScopeIsWithinScope(): void
    {
        $type = $this->faker->word();

        $scope = new TypeScope($type);

        $otherScope = new RelationScope($type);

        static::assertTrue($scope->isWithinScope($otherScope));
    }
}
