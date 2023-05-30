<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Submission\Video;

use App\Rules\Wiki\Submission\SubmissionRule;
use App\Rules\Wiki\Submission\Video\VideoColorPrimariesStreamRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Class VideoColorPrimariesStreamTest.
 */
class VideoColorPrimariesStreamTest extends TestCase
{
    use WithFaker;

    /**
     * The Video Color Primaries Stream Rule shall fail if the color primaries is not in the list of accepted values.
     *
     * @return void
     */
    public function testFailsWhenColorPrimariesIsNotAccepted(): void
    {
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
                        'color_primaries' => $this->faker->word(),
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new VideoColorPrimariesStreamRule()],
        );

        static::assertFalse($validator->passes());

        Process::assertRan(SubmissionRule::formatFfprobeCommand($file));
    }

    /**
     * The Video Color Primaries Stream Rule shall pass if the color primaries is in the list of accepted values.
     *
     * @return void
     */
    public function testPassesWhenColorPrimariesIsAccepted(): void
    {
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
                        'color_primaries' => Arr::random(['bt709', 'smpte170m', 'bt470bg']),
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new VideoColorPrimariesStreamRule()],
        );

        static::assertTrue($validator->passes());

        Process::assertRan(SubmissionRule::formatFfprobeCommand($file));
    }
}
