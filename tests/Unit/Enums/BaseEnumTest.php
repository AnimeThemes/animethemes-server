<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\BaseEnum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class BaseEnumTest.
 */
class BaseEnumTest extends TestCase
{
    use WithFaker;

    /**
     * An enum instance shall be resolved by description.
     *
     * @return void
     */
    public function testFromDescriptionInstance()
    {
        $enum = new class($this->faker->numberBetween(0, 2)) extends BaseEnum
        {
            public const ZERO = 0;
            public const ONE = 1;
            public const TWO = 2;
        };

        $fromDescription = $enum::fromDescription($enum->description);

        static::assertInstanceOf(get_class($enum), $fromDescription);
    }

    /**
     * If the provided description does not match an instance, an enum instance shall not be resolved.
     *
     * @return void
     */
    public function testFromDescriptionNullable()
    {
        $enum = new class($this->faker->numberBetween(0, 2)) extends BaseEnum
        {
            public const ZERO = 0;
            public const ONE = 1;
            public const TWO = 2;
        };

        $fromDescription = $enum::fromDescription(Str::random());

        static::assertNull($fromDescription);
    }
}
