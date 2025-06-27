<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Api;

use App\Rules\Api\DelimitedRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Class DelimitedTest.
 */
class DelimitedTest extends TestCase
{
    use WithFaker;

    /**
     * The Delimited Rule shall pass if all values pass the given rules.
     *
     * @return void
     */
    public function test_passes_if_all_values_pass(): void
    {
        $attribute = $this->faker->word();

        $values = collect($this->faker->words());

        $validator = Validator::make(
            [$attribute => $values->implode(',')],
            [$attribute => new DelimitedRule(['required', 'string'])]
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Delimited Rule shall fail if there exist duplicate values.
     *
     * @return void
     */
    public function test_fails_for_duplicate_values(): void
    {
        $attribute = $this->faker->word();

        $duplicate = $this->faker->word();

        $values = collect([$duplicate, $this->faker->word(), $duplicate]);

        $validator = Validator::make(
            [$attribute => $values->implode(',')],
            [$attribute => new DelimitedRule(['required', 'string'])]
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Delimited Rule shall fail if any value fails a rule.
     *
     * @return void
     */
    public function test_fails_for_invalid_value(): void
    {
        $attribute = $this->faker->word();

        $values = collect([$this->faker->randomDigitNotNull(), $this->faker->word(), $this->faker->randomDigitNotNull()]);

        $validator = Validator::make(
            [$attribute => $values->implode(',')],
            [$attribute => new DelimitedRule(['required', 'integer'])]
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Delimited Rule shall validate empty values.
     *
     * @return void
     */
    public function test_validates_empty_values(): void
    {
        $attribute = $this->faker->word();

        $values = collect(array_merge($this->faker->words(), ['']));

        $validator = Validator::make(
            [$attribute => $values->implode(',')],
            [$attribute => new DelimitedRule(['required', 'string'])]
        );

        static::assertFalse($validator->passes());
    }
}
