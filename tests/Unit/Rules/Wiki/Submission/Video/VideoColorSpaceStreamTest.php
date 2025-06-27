<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Submission\Video;

use App\Constants\FeatureConstants;
use App\Rules\Wiki\Submission\SubmissionRule;
use App\Rules\Wiki\Submission\Video\VideoColorSpaceStreamRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class VideoColorSpaceStreamTest.
 */
class VideoColorSpaceStreamTest extends TestCase
{
    use WithFaker;

    /**
     * The Video Color Space Stream Rule shall fail if the color space is not in the list of accepted values.
     *
     * @return void
     */
    public function test_fails_when_color_space_is_not_accepted(): void
    {
        Feature::activate(FeatureConstants::VIDEO_COLOR_SPACE_STREAM, 'bt709,smpte170m,bt470bg');

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
                        'color_space' => $this->faker->word(),
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new VideoColorSpaceStreamRule()],
        );

        static::assertFalse($validator->passes());

        Process::assertRan(SubmissionRule::formatFfprobeCommand($file));
    }

    /**
     * The Video Color Space Stream Rule shall pass if the color space is in the list of accepted values.
     *
     * @return void
     */
    public function test_passes_when_color_space_is_accepted(): void
    {
        Feature::activate(FeatureConstants::VIDEO_COLOR_SPACE_STREAM, 'bt709,smpte170m,bt470bg');

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
                        'color_space' => Arr::random(['bt709', 'smpte170m', 'bt470bg']),
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new VideoColorSpaceStreamRule()],
        );

        static::assertTrue($validator->passes());

        Process::assertRan(SubmissionRule::formatFfprobeCommand($file));
    }
}
