<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Api;

use App\Enums\BaseEnum;
use App\Rules\Api\EnumDescriptionRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class EnumDescriptionTest.
 */
class EnumDescriptionTest extends TestCase
{
    use WithFaker;

    /**
     * The Enum Description Rule shall return true if an enum description is provided.
     *
     * @return void
     */
    public function testPassesIfEnumDescription(): void
    {
        $enum = new class($this->faker->numberBetween(0, 2)) extends BaseEnum
        {
            public const ZERO = 0;
            public const ONE = 1;
            public const TWO = 2;
        };

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $enum->description],
            [$attribute => new EnumDescriptionRule(get_class($enum))]
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Enum Description Rule shall return false if an enum value is provided.
     *
     * @return void
     */
    public function testFailsIfEnumValue(): void
    {
        $enum = new class($this->faker->numberBetween(0, 2)) extends BaseEnum
        {
            public const ZERO = 0;
            public const ONE = 1;
            public const TWO = 2;
        };

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $enum->value],
            [$attribute => new EnumDescriptionRule(get_class($enum))]
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Enum Description Rule shall return false if an enum description is not provided.
     *
     * @return void
     */
    public function testFailsIfString(): void
    {
        $enum = new class($this->faker->numberBetween(0, 2)) extends BaseEnum
        {
            public const ZERO = 0;
            public const ONE = 1;
            public const TWO = 2;
        };

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => Str::random()],
            [$attribute => new EnumDescriptionRule(get_class($enum))]
        );

        static::assertFalse($validator->passes());
    }
}
