<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Submission\Audio;

use App\Rules\Wiki\Submission\Audio\AudioLoudnessTruePeakStreamRule;
use App\Rules\Wiki\Submission\SubmissionRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Class AudioLoudnessTruePeakStreamTest.
 */
class AudioLoudnessTruePeakStreamTest extends TestCase
{
    use WithFaker;

    /**
     * The Audio Loudness True Peak Stream Rule shall fail if the true peak is greater than to 0.
     *
     * @return void
     */
    public function testFailsWhenTruePeakIsNotExpected(): void
    {
        $file = UploadedFile::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        Process::fake([
            SubmissionRule::formatLoudnessCommand($file) => Process::result(errorOutput: json_encode([
                'input_i' => $this->faker->randomFloat(),
                'input_tp' => $this->faker->randomFloat(min: 0.1),
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
                        'codec_type' => 'audio',
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new AudioLoudnessTruePeakStreamRule()],
        );

        static::assertFalse($validator->passes());

        Process::assertRan(SubmissionRule::formatLoudnessCommand($file));
    }

    /**
     * The Audio Loudness True Peak Stream Rule shall pass if the true peak is less than or equal to 0.
     *
     * @return void
     */
    public function testPassesWhenTruePeakIsExpected(): void
    {
        $file = UploadedFile::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        Process::fake([
            SubmissionRule::formatLoudnessCommand($file) => Process::result(errorOutput: json_encode([
                'input_i' => $this->faker->numberBetween(),
                'input_tp' => $this->faker->randomFloat(min: -20, max: 0.1),
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
                        'codec_type' => 'audio',
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new AudioLoudnessTruePeakStreamRule()],
        );

        static::assertTrue($validator->passes());

        Process::assertRan(SubmissionRule::formatLoudnessCommand($file));
    }
}
