<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Api;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Sort\Sort;
use App\Rules\Api\DistinctIgnoringDirectionRule;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class DistinctIgnoringDirectionTest.
 */
class DistinctIgnoringDirectionTest extends TestCase
{
    use WithFaker;

    /**
     * The Distinct Ignoring Direction Rule shall return false if there exist duplicate sort keys.
     *
     * @return void
     */
    public function testFailsIfDuplicateSort()
    {
        $key = $this->faker->word();

        $rule = new DistinctIgnoringDirectionRule();

        $sorts = collect()->pad($this->faker->randomDigitNotNull(), $key);

        static::assertFalse($rule->passes($this->faker->word(), $sorts->join(',')));
    }

    /**
     * The Distinct Ignoring Direction Rule shall return false if there exists duplicate sort keys of differing directions.
     *
     * @return void
     */
    public function testFailsIfDuplicateSortDifferentDirection()
    {
        $key = $this->faker->word();

        $rule = new DistinctIgnoringDirectionRule();

        $sort = new Sort($key);

        $sorts = [];

        foreach (Direction::getInstances() as $direction) {
            $sorts[] = $sort->format($direction);
        }

        static::assertFalse($rule->passes($this->faker->word(), implode(',', $sorts)));
    }

    /**
     * The Distinct Ignoring Direction Rule shall return true if there exist no duplicate sort keys.
     *
     * @return void
     */
    public function testPassesIfNoDuplicates()
    {
        $rule = new DistinctIgnoringDirectionRule();

        $sorts = collect($this->faker->words($this->faker->randomDigitNotNull()))->unique();

        static::assertTrue($rule->passes($this->faker->word(), $sorts->join(',')));
    }
}
