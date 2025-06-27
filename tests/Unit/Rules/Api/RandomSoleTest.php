<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Api;

use App\Http\Api\Criteria\Sort\RandomCriteria;
use App\Rules\Api\RandomSoleRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Class RandomSoleTest.
 */
class RandomSoleTest extends TestCase
{
    use WithFaker;

    /**
     * The Random Sole Rule shall return false if the random key is provided and is not the sole sort.
     *
     * @return void
     */
    public function testFailsIfRandomIsNotSoleSort(): void
    {
        $sorts = $this->faker->words($this->faker->randomDigitNotNull());

        $sorts[] = RandomCriteria::PARAM_VALUE;

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => implode(',', $sorts)],
            [$attribute => new RandomSoleRule()]
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Random Sole Rule shall return true if the random key is not provided.
     *
     * @return void
     */
    public function testPassesIfRandomIsNotIncluded(): void
    {
        $sorts = $this->faker->words($this->faker->randomDigitNotNull());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => implode(',', $sorts)],
            [$attribute => new RandomSoleRule()]
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Random Sole Rule shall return true if the random key is the only sort provided.
     *
     * @return void
     */
    public function testPassesIfRandomIsSoleSort(): void
    {
        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => RandomCriteria::PARAM_VALUE],
            [$attribute => new RandomSoleRule()]
        );

        static::assertTrue($validator->passes());
    }
}
