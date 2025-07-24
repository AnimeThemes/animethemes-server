<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Submission\Format;

use App\Actions\Storage\Wiki\UploadedFileAction;
use App\Rules\Wiki\Submission\Format\EncoderNameFormatRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class EncoderNameFormatTest extends TestCase
{
    use WithFaker;

    /**
     * The Encoder Name Format Rule shall fail if the encoder is not FFmpeg.
     */
    public function testFailsWhenEncoderNameIsNotFFmpeg(): void
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
                'format' => [
                    'tags' => [
                        'ENCODER' => $this->faker->word(),
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new EncoderNameFormatRule()],
        );

        static::assertFalse($validator->passes());

        Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
    }

    /**
     * The Encoder Name Format Rule shall pass if the encoder is FFmpeg.
     */
    public function testPassesWhenEncoderNameIsFFmpeg(): void
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
                'format' => [
                    'tags' => [
                        'ENCODER' => "Lavf{$this->faker->numberBetween()}",
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new EncoderNameFormatRule()],
        );

        static::assertTrue($validator->passes());

        Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
    }
}
