<?php

namespace Tests\Unit\Rules\Billing;

use App\Enums\Filter\AllowedDateFormat;
use App\Rules\Billing\TransparencyDateRule;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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

        $this->assertFalse($rule->passes($this->faker->word(), $this->faker->word()));
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

        $this->assertFalse($rule->passes($this->faker->word(), $this->faker->numberBetween()));
    }

    /**
     * The Transparency Date Rule shall return false if the date format is not 'Y-m'.
     *
     * @return void
     */
    public function testInvalidDateFormat()
    {
        $format = null;

        while ($format == null) {
            $format_candidate = AllowedDateFormat::getRandomInstance();
            if (! $format_candidate->is(AllowedDateFormat::WITH_MONTH)) {
                $format = $format_candidate;
            }
        }

        $validDates = collect([Carbon::now()]);

        $rule = new TransparencyDateRule($validDates);

        $formatted_date = Carbon::now()->format($format);

        $this->assertFalse($rule->passes($this->faker->word(), $formatted_date));
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

        $formatted_date = Carbon::now()->subMonths($this->faker->randomDigitNotNull)->format(AllowedDateFormat::WITH_MONTH);

        $this->assertFalse($rule->passes($this->faker->word(), $formatted_date));
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

        $formatted_date = Carbon::now()->format(AllowedDateFormat::WITH_MONTH);

        $this->assertTrue($rule->passes($this->faker->word(), $formatted_date));
    }
}
