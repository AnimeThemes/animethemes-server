<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Billing;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Rules\Billing\TransparencyDateRule;
use DateTimeInterface;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

/**
 * Class TransparencyDateTest.
 */
class TransparencyDateTest extends TestCase
{
    use WithFaker;

    /**
     * The Transparency Date Rule shall return false if the value is a string.
     *
     * @return void
     */
    public function testStringInvalid(): void
    {
        $validDates = collect([Date::now()]);

        $rule = new TransparencyDateRule($validDates);

        static::assertFalse($rule->passes($this->faker->word(), $this->faker->word()));
    }

    /**
     * The Transparency Date Rule shall return false if the value is numeric.
     *
     * @return void
     */
    public function testIntInvalid(): void
    {
        $validDates = collect([Date::now()]);

        $rule = new TransparencyDateRule($validDates);

        static::assertFalse($rule->passes($this->faker->word(), $this->faker->numberBetween()));
    }

    /**
     * The Transparency Date Rule shall return false if the date format is not 'Y-m'.
     *
     * @return void
     */
    public function testInvalidDateFormat(): void
    {
        $validDates = collect([Date::now()]);

        $rule = new TransparencyDateRule($validDates);

        $formattedDate = Date::now()->format(DateTimeInterface::RFC822);

        static::assertFalse($rule->passes($this->faker->word(), $formattedDate));
    }

    /**
     * The Transparency Date Rule shall return false if the date is not a valid option.
     *
     * @return void
     */
    public function testInvalidDateOption(): void
    {
        $validDates = collect([Date::now()]);

        $rule = new TransparencyDateRule($validDates);

        $formattedDate = Date::now()->subMonths($this->faker->randomDigitNotNull())->format(AllowedDateFormat::YM);

        static::assertFalse($rule->passes($this->faker->word(), $formattedDate));
    }

    /**
     * The Transparency Date Rule shall return true if the date is a valid option.
     *
     * @return void
     */
    public function testValidDateOption(): void
    {
        $validDates = collect([Date::now()]);

        $rule = new TransparencyDateRule($validDates);

        $formattedDate = Date::now()->format(AllowedDateFormat::YM);

        static::assertTrue($rule->passes($this->faker->word(), $formattedDate));
    }
}
