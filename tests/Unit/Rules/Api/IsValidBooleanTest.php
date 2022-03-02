<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Api;

use App\Rules\Api\IsValidBoolean;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * IsValidBooleanTest.
 */
class IsValidBooleanTest extends TestCase
{
    use WithFaker;

    /**
     * The Is Valid Boolean Rule shall return true if a boolean is provided.
     *
     * @return void
     */
    public function testPassesIfBoolean(): void
    {
        $boolean = $this->faker->boolean();

        $rule = new IsValidBoolean();

        static::assertTrue($rule->passes($this->faker->word(), $boolean));
    }

    /**
     * The Is Valid Boolean Rule shall return true if "true" or "false" is provided.
     *
     * @return void
     */
    public function testPassesIfBooleanString(): void
    {
        $booleanString = $this->faker->boolean() ? 'true' : 'false';

        $rule = new IsValidBoolean();

        static::assertTrue($rule->passes($this->faker->word(), $booleanString));
    }

    /**
     * The Is Valid Boolean Rule shall return true if 1 or 0 is provided.
     *
     * @return void
     */
    public function testPassesIfBooleanInteger(): void
    {
        $booleanInteger = $this->faker->boolean() ? 1 : 0;

        $rule = new IsValidBoolean();

        static::assertTrue($rule->passes($this->faker->word(), $booleanInteger));
    }

    /**
     * The Is Valid Boolean Rule shall return true if "on" or "off" is provided.
     *
     * @return void
     */
    public function testPassesIfBooleanCheckbox(): void
    {
        $booleanCheckbox = $this->faker->boolean() ? 'on' : 'off';

        $rule = new IsValidBoolean();

        static::assertTrue($rule->passes($this->faker->word(), $booleanCheckbox));
    }

    /**
     * The Is Valid Boolean Rule shall return false a string is provided.
     *
     * @return void
     */
    public function testFailsIfString(): void
    {
        $rule = new IsValidBoolean();

        static::assertFalse($rule->passes($this->faker->word(), Str::random()));
    }

    /**
     * The Is Valid Boolean Rule shall return false a number is provided.
     *
     * @return void
     */
    public function testFailsIfNumber(): void
    {
        $number = $this->faker->numberBetween(2, 9);

        $rule = new IsValidBoolean();

        static::assertFalse($rule->passes($this->faker->word(), $number));
    }
}
