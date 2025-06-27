<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Scope;

use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\RelationScope;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class RelationScopeTest.
 */
class RelationScopeTest extends TestCase
{
    use WithFaker;

    /**
     * A global scope shall not be within the scope of a relation scope.
     *
     * @return void
     */
    public function test_global_scope_is_not_within_scope(): void
    {
        $scope = new RelationScope($this->faker->word());

        $otherScope = new GlobalScope();

        static::assertFalse($scope->isWithinScope($otherScope));
    }

    /**
     * A type scope shall not be within the scope of a relation scope.
     *
     * @return void
     */
    public function test_type_scope_is_not_within_scope(): void
    {
        $scope = new RelationScope($this->faker->word());

        $otherScope = new TypeScope($this->faker->word());

        static::assertFalse($scope->isWithinScope($otherScope));
    }

    /**
     * A relation scope of an unequal value shall not be within the scope of a relation scope.
     *
     * @return void
     */
    public function test_unequal_relation_is_not_within_scope(): void
    {
        $scope = new RelationScope($this->faker->word());

        $otherScope = new RelationScope($this->faker->word());

        static::assertFalse($scope->isWithinScope($otherScope));
    }

    /**
     * A relation scope of an equal value shall be within the scope of a relation scope.
     *
     * @return void
     */
    public function test_relation_is_within_scope(): void
    {
        $relation = $this->faker->word();

        $scope = new RelationScope($relation);

        $otherScope = new RelationScope($relation);

        static::assertTrue($scope->isWithinScope($otherScope));
    }
}
