<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Api;

use App\Rules\Api\EnumLocalizedNameRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Unit\Enums\LocalizedEnum;

/**
 * Class EnumLocalizedNameTest.
 */
class EnumLocalizedNameTest extends TestCase
{
    use WithFaker;

    /**
     * The Enum Description Rule shall return true if an enum description is provided.
     *
     * @return void
     */
    public function testPassesIfEnumDescription(): void
    {
        $enum = Arr::random(LocalizedEnum::cases());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $enum->localize()],
            [$attribute => new EnumLocalizedNameRule(LocalizedEnum::class)]
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
        $enum = Arr::random(LocalizedEnum::cases());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $enum->value],
            [$attribute => new EnumLocalizedNameRule(LocalizedEnum::class)]
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
        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => Str::random()],
            [$attribute => new EnumLocalizedNameRule(LocalizedEnum::class)]
        );

        static::assertFalse($validator->passes());
    }
}
