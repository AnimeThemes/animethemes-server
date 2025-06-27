<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Storage\Base;

use App\Actions\Storage\Base\MoveResults;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class MoveResultsTest.
 */
class MoveResultsTest extends TestCase
{
    use WithFaker;

    /**
     * The Action result has failed if there are no moves.
     *
     * @return void
     */
    public function test_default(): void
    {
        $video = Video::factory()->createOne();

        $moveResults = new MoveResults($video, $this->faker->word(), $this->faker->word());

        $result = $moveResults->toActionResult();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Action result has failed if any moves have returned false.
     *
     * @return void
     */
    public function test_failed(): void
    {
        $video = Video::factory()->createOne();

        $moves = [];

        foreach (range(0, $this->faker->randomDigitNotNull()) as $ignored) {
            $moves[$this->faker->word()] = true;
        }

        foreach (range(0, $this->faker->randomDigitNotNull()) as $ignored) {
            $moves[$this->faker->word()] = false;
        }

        $moveResults = new MoveResults($video, $this->faker->word(), $this->faker->word(), $moves);

        $result = $moveResults->toActionResult();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Action result has passed if all moves have returned true.
     *
     * @return void
     */
    public function test_passed(): void
    {
        $video = Video::factory()->createOne();

        $moves = [];

        foreach (range(0, $this->faker->randomDigitNotNull()) as $ignored) {
            $moves[$this->faker->word()] = true;
        }

        $moveResults = new MoveResults($video, $this->faker->word(), $this->faker->word(), $moves);

        $result = $moveResults->toActionResult();

        static::assertTrue($result->getStatus() === ActionStatus::PASSED);
    }
}
