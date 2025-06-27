<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Submission\Video;

use App\Constants\FeatureConstants;
use App\Rules\Wiki\Submission\SubmissionRule;
use App\Rules\Wiki\Submission\Video\VideoPixelFormatStreamRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class VideoPixelFormatStreamTest.
 */
class VideoPixelFormatStreamTest extends TestCase
{
    use WithFaker;

    /**
     * The Video Pixel Format Stream Rule shall fail if the pixel format is not yuv420p.
     *
     * @return void
     */
    public function test_fails_when_codec_is_not_yuv420p(): void
    {
        Feature::activate(FeatureConstants::VIDEO_PIXEL_FORMAT_STREAM, 'yuv420p');

        $file = UploadedFile::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        Process::fake([
            SubmissionRule::formatLoudnessCommand($file) => Process::result(errorOutput: json_encode([
                'input_i' => $this->faker->randomFloat(),
                'input_tp' => $this->faker->randomFloat(),
                'input_lra' => $this->faker->randomFloat(),
                'input_thresh' => $this->faker->randomFloat(),
                'output_i' => $this->faker->randomFloat(),
                'output_tp' => $this->faker->randomFloat(),
                'output_lra' => $this->faker->randomFloat(),
                'output_thresh' => $this->faker->randomFloat(),
                'normalization_type' => 'dynamic',
                'target_offset' => $this->faker->randomFloat(),
            ])),
            SubmissionRule::formatFfprobeCommand($file) => Process::result(json_encode([
                'streams' => [
                    0 => [
                        'codec_type' => 'video',
                        'pix_fmt' => $this->faker->randomDigitNot(2),
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new VideoPixelFormatStreamRule()],
        );

        static::assertFalse($validator->passes());

        Process::assertRan(SubmissionRule::formatFfprobeCommand($file));
    }

    /**
     * The Video Pixel Format Stream Rule shall pass if the pixel format is yuv420p.
     *
     * @return void
     */
    public function test_passes_when_codec_is_vp9(): void
    {
        Feature::activate(FeatureConstants::VIDEO_PIXEL_FORMAT_STREAM, 'yuv420p');

        $file = UploadedFile::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        Process::fake([
            SubmissionRule::formatLoudnessCommand($file) => Process::result(errorOutput: json_encode([
                'input_i' => $this->faker->randomFloat(),
                'input_tp' => $this->faker->randomFloat(),
                'input_lra' => $this->faker->randomFloat(),
                'input_thresh' => $this->faker->randomFloat(),
                'output_i' => $this->faker->randomFloat(),
                'output_tp' => $this->faker->randomFloat(),
                'output_lra' => $this->faker->randomFloat(),
                'output_thresh' => $this->faker->randomFloat(),
                'normalization_type' => 'dynamic',
                'target_offset' => $this->faker->randomFloat(),
            ])),
            SubmissionRule::formatFfprobeCommand($file) => Process::result(json_encode([
                'streams' => [
                    0 => [
                        'codec_type' => 'video',
                        'pix_fmt' => 'yuv420p',
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new VideoPixelFormatStreamRule()],
        );

        static::assertTrue($validator->passes());

        Process::assertRan(SubmissionRule::formatFfprobeCommand($file));
    }
}
