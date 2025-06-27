<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Storage\Base;

use App\Actions\Storage\Base\DeleteResults;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class DeleteResultsTest.
 */
class DeleteResultsTest extends TestCase
{
    use WithFaker;

    /**
     * The Action result has failed if there are no deletions.
     *
     * @return void
     */
    public function test_default(): void
    {
        $video = Video::factory()->createOne();

        $deleteResults = new DeleteResults($video);

        $result = $deleteResults->toActionResult();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Action result has failed if any deletions have returned false.
     *
     * @return void
     */
    public function test_failed(): void
    {
        $video = Video::factory()->createOne();

        $deletions = [];

        foreach (range(0, $this->faker->randomDigitNotNull()) as $ignored) {
            $deletions[$this->faker->word()] = true;
        }

        foreach (range(0, $this->faker->randomDigitNotNull()) as $ignored) {
            $deletions[$this->faker->word()] = false;
        }

        $deleteResults = new DeleteResults($video, $deletions);

        $result = $deleteResults->toActionResult();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Action result has passed if all deletions have returned true.
     *
     * @return void
     */
    public function test_passed(): void
    {
        $video = Video::factory()->createOne();

        $deletions = [];

        foreach (range(0, $this->faker->randomDigitNotNull()) as $ignored) {
            $deletions[$this->faker->word()] = true;
        }

        $deleteResults = new DeleteResults($video, $deletions);

        $result = $deleteResults->toActionResult();

        static::assertTrue($result->getStatus() === ActionStatus::PASSED);
    }
}
