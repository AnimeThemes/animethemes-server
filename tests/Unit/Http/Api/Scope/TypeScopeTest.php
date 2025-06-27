<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Scope;

use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\RelationScope;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class TypeScopeTest.
 */
class TypeScopeTest extends TestCase
{
    use WithFaker;

    /**
     * A global scope shall not be within the scope of a type scope.
     *
     * @return void
     */
    public function test_global_scope_is_not_within_scope(): void
    {
        $scope = new TypeScope($this->faker->word());

        $otherScope = new GlobalScope();

        static::assertFalse($scope->isWithinScope($otherScope));
    }

    /**
     * A type scope of unequal value shall not be within the scope of a type scope.
     *
     * @return void
     */
    public function test_type_scope_is_not_within_scope(): void
    {
        $scope = new TypeScope($this->faker->word());

        $otherScope = new TypeScope($this->faker->word());

        static::assertFalse($scope->isWithinScope($otherScope));
    }

    /**
     * A type scope of equal value shall be within the scope of a type scope.
     *
     * @return void
     */
    public function test_type_scope_is_within_scope(): void
    {
        $type = $this->faker->word();

        $scope = new TypeScope($type);

        $otherScope = new TypeScope($type);

        static::assertTrue($scope->isWithinScope($otherScope));
    }

    /**
     * A relation scope of unequal type shall not be within the scope of a type scope.
     *
     * @return void
     */
    public function test_relation_scope_is_not_within_scope(): void
    {
        $scope = new TypeScope($this->faker->word());

        $otherScope = new RelationScope($this->faker->word());

        static::assertFalse($scope->isWithinScope($otherScope));
    }

    /**
     * A relation scope of equal value shall be within the scope of a type scope.
     *
     * @return void
     */
    public function test_relation_scope_is_within_scope(): void
    {
        $type = $this->faker->word();

        $scope = new TypeScope($type);

        $otherScope = new RelationScope($type);

        static::assertTrue($scope->isWithinScope($otherScope));
    }
}
