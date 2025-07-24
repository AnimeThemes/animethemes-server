<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Submission\Format;

use App\Actions\Storage\Wiki\UploadedFileAction;
use App\Rules\Wiki\Submission\Format\TotalStreamsFormatRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TotalStreamsFormatTest extends TestCase
{
    use WithFaker;

    /**
     * The Total Streams Format Rule shall fail if the total stream count is not equal to the expected count.
     */
    public function testFailsWhenCountIsNotExpected(): void
    {
        $file = UploadedFile::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        Process::fake([
            UploadedFileAction::formatLoudnessCommand($file) => Process::result(errorOutput: json_encode([
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
            UploadedFileAction::formatFfprobeCommand($file) => Process::result(json_encode([
                'streams' => [
                    0 => [
                        'index' => 0,
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new TotalStreamsFormatRule(2)],
        );

        static::assertFalse($validator->passes());

        Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
    }

    /**
     * The Total Streams Format Rule shall pass if the total stream count is equal to the expected count.
     */
    public function testPassesWhenCountIsExpected(): void
    {
        $file = UploadedFile::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        Process::fake([
            UploadedFileAction::formatLoudnessCommand($file) => Process::result(errorOutput: json_encode([
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
            UploadedFileAction::formatFfprobeCommand($file) => Process::result(json_encode([
                'streams' => [
                    0 => [
                        'index' => 0,
                    ],
                    1 => [
                        'index' => 1,
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new TotalStreamsFormatRule(2)],
        );

        static::assertTrue($validator->passes());

        Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
    }
}
