<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Submission\Format;

use App\Constants\FeatureConstants;
use App\Rules\Wiki\Submission\Format\EncoderVersionFormatRule;
use App\Rules\Wiki\Submission\SubmissionRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class EncoderVersionFormatTest.
 */
class EncoderVersionFormatTest extends TestCase
{
    use WithFaker;

    /**
     * The Encoder Version Format Rule shall fail if the encoder version is older than the required version.
     *
     * @return void
     */
    public function testFailsWhenEncoderVersionIsOlderThanRequired(): void
    {
        Feature::activate(FeatureConstants::REQUIRED_ENCODER_VERSION, 'Lavf59.27.100');

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
                'format' => [
                    'tags' => [
                        'ENCODER' => 'Lavf58.76.100',
                    ],
                ],
            ]))
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new EncoderVersionFormatRule()],
        );

        static::assertFalse($validator->passes());

        Process::assertRan(SubmissionRule::formatFfprobeCommand($file));
    }

    /**
     * The Encoder Version Format Rule shall pass if the encoder version is up to date with the required version.
     *
     * @return void
     */
    public function testFailsWhenEncoderVersionIsUpToDate(): void
    {
        Feature::activate(FeatureConstants::REQUIRED_ENCODER_VERSION, 'Lavf59.27.100');

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
                'format' => [
                    'tags' => [
                        'ENCODER' => 'Lavf59.27.100',
                    ],
                ],
            ]))
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new EncoderVersionFormatRule()],
        );

        static::assertTrue($validator->passes());

        Process::assertRan(SubmissionRule::formatFfprobeCommand($file));
    }
}
