<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Submission\Audio;

use App\Rules\Wiki\Submission\Audio\AudioSampleRateStreamRule;
use App\Rules\Wiki\Submission\SubmissionRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Class AudioSampleRateStreamTest.
 */
class AudioSampleRateStreamTest extends TestCase
{
    use WithFaker;

    /**
     * The Audio Sample Rate Stream Rule shall fail if the sample rate is not 48k.
     *
     * @return void
     */
    public function testFailsWhenSampleRateIsNotExpected(): void
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
                        'codec_type' => 'audio',
                        'sample_rate' => $this->faker->randomDigitNotNull(),
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new AudioSampleRateStreamRule()],
        );

        static::assertFalse($validator->passes());

        Process::assertRan(SubmissionRule::formatFfprobeCommand($file));
    }

    /**
     * The Audio Sample Rate Stream Rule shall pass if the sample rate is 48k.
     *
     * @return void
     */
    public function testPassesWhenSampleRateIsExpected(): void
    {
        $index = $this->faker->randomDigitNotNull();

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
                        'codec_type' => 'audio',
                        'sample_rate' => '48000',
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new AudioSampleRateStreamRule()],
        );

        static::assertTrue($validator->passes());

        Process::assertRan(SubmissionRule::formatFfprobeCommand($file));
    }
}
