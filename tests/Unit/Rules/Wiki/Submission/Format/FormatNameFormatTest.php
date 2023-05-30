<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Submission\Format;

use App\Rules\Wiki\Submission\Format\FormatNameFormatRule;
use App\Rules\Wiki\Submission\SubmissionRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Class FormatNameFormatTest.
 */
class FormatNameFormatTest extends TestCase
{
    use WithFaker;

    /**
     * The Format Name Format Rule shall fail if the format name is not expected.
     *
     * @return void
     */
    public function testFailsWhenFormatNameIsNotExpected(): void
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
                'format' => [
                    'format_name' => $this->faker->unique()->word(),
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new FormatNameFormatRule($this->faker->unique()->word())],
        );

        static::assertFalse($validator->passes());

        Process::assertRan(SubmissionRule::formatFfprobeCommand($file));
    }

    /**
     * The Format Name Format Rule shall pass if the format name is expected.
     *
     * @return void
     */
    public function testPassesWhenFormatNameIsExpected(): void
    {
        $formatName = $this->faker->word();

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
                    'format_name' => $formatName,
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new FormatNameFormatRule($formatName)],
        );

        static::assertTrue($validator->passes());

        Process::assertRan(SubmissionRule::formatFfprobeCommand($file));
    }
}
