<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Storage\Base;

use App\Actions\Storage\Base\PruneResults;
use App\Enums\Actions\ActionStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class PruneResultsTest.
 */
class PruneResultsTest extends TestCase
{
    use WithFaker;

    /**
     * The Action result has failed if there are no prunings.
     *
     * @return void
     */
    public function test_default(): void
    {
        $pruneResults = new PruneResults($this->faker->word());

        $result = $pruneResults->toActionResult();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Action result has failed if any prunings have returned false.
     *
     * @return void
     */
    public function test_failed(): void
    {
        $prunings = [];

        foreach (range(0, $this->faker->randomDigitNotNull()) as $ignored) {
            $prunings[$this->faker->word()] = true;
        }

        foreach (range(0, $this->faker->randomDigitNotNull()) as $ignored) {
            $prunings[$this->faker->word()] = false;
        }

        $pruneResults = new PruneResults($this->faker->word(), $prunings);

        $result = $pruneResults->toActionResult();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Action result has passed if all prunings have returned true.
     *
     * @return void
     */
    public function test_passed(): void
    {
        $prunings = [];

        foreach (range(0, $this->faker->randomDigitNotNull()) as $ignored) {
            $prunings[$this->faker->word()] = true;
        }

        $pruneResults = new PruneResults($this->faker->word(), $prunings);

        $result = $pruneResults->toActionResult();

        static::assertTrue($result->getStatus() === ActionStatus::PASSED);
    }
}
