<?php

declare(strict_types=1);

namespace Rules\Billing;

use App\Enums\Filter\AllowedDateFormat;
use App\Rules\Billing\TransparencyDateRule;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Foundation\Testing\WithFaker;
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
    public function testStringInvalid()
    {
        $validDates = collect([Carbon::now()]);

        $rule = new TransparencyDateRule($validDates);

        static::assertFalse($rule->passes($this->faker->word(), $this->faker->word()));
    }

    /**
     * The Transparency Date Rule shall return false if the value is numeric.
     *
     * @return void
     */
    public function testIntInvalid()
    {
        $validDates = collect([Carbon::now()]);

        $rule = new TransparencyDateRule($validDates);

        static::assertFalse($rule->passes($this->faker->word(), $this->faker->numberBetween()));
    }

    /**
     * The Transparency Date Rule shall return false if the date format is not 'Y-m'.
     *
     * @return void
     */
    public function testInvalidDateFormat()
    {
        $validDates = collect([Carbon::now()]);

        $rule = new TransparencyDateRule($validDates);

        $formattedDate = Carbon::now()->format(DateTimeInterface::RFC822);

        static::assertFalse($rule->passes($this->faker->word(), $formattedDate));
    }

    /**
     * The Transparency Date Rule shall return false if the date is not a valid option.
     *
     * @return void
     */
    public function testInvalidDateOption()
    {
        $validDates = collect([Carbon::now()]);

        $rule = new TransparencyDateRule($validDates);

        $formattedDate = Carbon::now()->subMonths($this->faker->randomDigitNotNull)->format(AllowedDateFormat::YM);

        static::assertFalse($rule->passes($this->faker->word(), $formattedDate));
    }

    /**
     * The Transparency Date Rule shall return true if the date is a valid option.
     *
     * @return void
     */
    public function testValidDateOption()
    {
        $validDates = collect([Carbon::now()]);

        $rule = new TransparencyDateRule($validDates);

        $formattedDate = Carbon::now()->format(AllowedDateFormat::YM);

        static::assertTrue($rule->passes($this->faker->word(), $formattedDate));
    }
}
