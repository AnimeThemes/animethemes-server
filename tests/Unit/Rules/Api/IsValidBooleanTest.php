<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Api;

use App\Rules\Api\IsValidBoolean;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * IsValidBooleanTest.
 */
class IsValidBooleanTest extends TestCase
{
    use WithFaker;

    /**
     * The Is Valid Boolean Rule shall return true if a boolean is provided.
     */
    public function testPassesIfBoolean(): void
    {
        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $this->faker->boolean()],
            [$attribute => new IsValidBoolean()]
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Is Valid Boolean Rule shall return true if "true" or "false" is provided.
     */
    public function testPassesIfBooleanString(): void
    {
        $booleanString = $this->faker->boolean() ? 'true' : 'false';

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $booleanString],
            [$attribute => new IsValidBoolean()]
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Is Valid Boolean Rule shall return true if 1 or 0 is provided.
     */
    public function testPassesIfBooleanInteger(): void
    {
        $booleanInteger = $this->faker->boolean() ? 1 : 0;

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $booleanInteger],
            [$attribute => new IsValidBoolean()]
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Is Valid Boolean Rule shall return true if "on" or "off" is provided.
     */
    public function testPassesIfBooleanCheckbox(): void
    {
        $booleanCheckbox = $this->faker->boolean() ? 'on' : 'off';

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $booleanCheckbox],
            [$attribute => new IsValidBoolean()]
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Is Valid Boolean Rule shall return false a string is provided.
     */
    public function testFailsIfString(): void
    {
        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $this->faker->word()],
            [$attribute => new IsValidBoolean()]
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Is Valid Boolean Rule shall return false a number is provided.
     */
    public function testFailsIfNumber(): void
    {
        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $this->faker->numberBetween(2, 9)],
            [$attribute => new IsValidBoolean()]
        );

        static::assertFalse($validator->passes());
    }
}
