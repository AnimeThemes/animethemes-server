<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Api;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Sort\Sort;
use App\Rules\Api\DistinctIgnoringDirectionRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
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
    public function testFailsIfDuplicateSort(): void
    {
        $key = $this->faker->word();

        $sorts = collect()->pad($this->faker->numberBetween(2, 9), $key);

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $sorts->join(',')],
            [$attribute => new DistinctIgnoringDirectionRule()]
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Distinct Ignoring Direction Rule shall return false if there exists duplicate sort keys of differing directions.
     *
     * @return void
     */
    public function testFailsIfDuplicateSortDifferentDirection(): void
    {
        $key = $this->faker->word();

        $sort = new Sort($key);

        $sorts = [];

        foreach (Direction::cases() as $direction) {
            $sorts[] = $sort->format($direction);
        }

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => implode(',', $sorts)],
            [$attribute => new DistinctIgnoringDirectionRule()]
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Distinct Ignoring Direction Rule shall return true if there exist no duplicate sort keys.
     *
     * @return void
     */
    public function testPassesIfNoDuplicates(): void
    {
        $sorts = collect($this->faker->words($this->faker->randomDigitNotNull()))->unique();

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $sorts->join(',')],
            [$attribute => new DistinctIgnoringDirectionRule()]
        );

        static::assertTrue($validator->passes());
    }
}
