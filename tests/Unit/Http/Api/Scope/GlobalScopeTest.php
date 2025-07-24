<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Scope;

use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\RelationScope;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GlobalScopeTest extends TestCase
{
    use WithFaker;

    /**
     * A global scope shall be within the scope of a global scope.
     */
    public function testGlobalScopeIsWithinScope(): void
    {
        $scope = new GlobalScope();

        $otherScope = new GlobalScope();

        static::assertTrue($scope->isWithinScope($otherScope));
    }

    /**
     * A type scope shall be within the scope of a global scope.
     */
    public function testTypeScopeIsWithinScope(): void
    {
        $scope = new GlobalScope();

        $otherScope = new TypeScope($this->faker->word());

        static::assertTrue($scope->isWithinScope($otherScope));
    }

    /**
     * A relation scope shall be within the scope of a global scope.
     */
    public function testRelationScopeIsWithinScope(): void
    {
        $scope = new GlobalScope();

        $otherScope = new RelationScope($this->faker->word());

        static::assertTrue($scope->isWithinScope($otherScope));
    }
}
