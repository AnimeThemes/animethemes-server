<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Models\Wiki\Video;

use App\Actions\Models\Wiki\Video\UploadResults;
use App\Enums\Actions\ActionStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class UploadResultsTest.
 */
class UploadResultsTest extends TestCase
{
    use WithFaker;

    /**
     * The Action result has failed if there are no uploads.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $uploadResults = new UploadResults();

        $result = $uploadResults->toActionResult();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Action result has failed if any uploads have returned false.
     *
     * @return void
     */
    public function testFailed(): void
    {
        $uploads = [];

        foreach (range(0, $this->faker->randomDigitNotNull()) as $ignored) {
            $uploads[$this->faker->word()] = $this->faker->filePath();
        }

        foreach (range(0, $this->faker->randomDigitNotNull()) as $ignored) {
            $uploads[$this->faker->word()] = false;
        }

        $uploadResults = new UploadResults($uploads);

        $result = $uploadResults->toActionResult();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Action result has passed if all uploads have returned the file path.
     *
     * @return void
     */
    public function testPassed(): void
    {
        $uploads = [];

        foreach (range(0, $this->faker->randomDigitNotNull()) as $ignored) {
            $uploads[$this->faker->word()] = $this->faker->filePath();
        }

        $uploadResults = new UploadResults($uploads);

        $result = $uploadResults->toActionResult();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
    }
}
