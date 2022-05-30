<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Api;

use App\Http\Api\Criteria\Sort\RandomCriteria;
use App\Rules\Api\RandomSoleRule;
use Illuminate\Foundation\Testing\WithFaker;
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

        $rule = new RandomSoleRule();

        static::assertFalse($rule->passes($this->faker->word(), implode(',', $sorts)));
    }

    /**
     * The Random Sole Rule shall return true if the random key is not provided.
     *
     * @return void
     */
    public function testPassesIfRandomIsNotIncluded(): void
    {
        $sorts = $this->faker->words($this->faker->randomDigitNotNull());

        $rule = new RandomSoleRule();

        static::assertTrue($rule->passes($this->faker->word(), implode(',', $sorts)));
    }

    /**
     * The Random Sole Rule shall return true if the random key is the only sort provided.
     *
     * @return void
     */
    public function testPassesIfRandomIsSoleSort(): void
    {
        $rule = new RandomSoleRule();

        static::assertTrue($rule->passes($this->faker->word(), RandomCriteria::PARAM_VALUE));
    }
}
