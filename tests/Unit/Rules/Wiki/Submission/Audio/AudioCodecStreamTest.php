<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Submission\Audio;

use App\Actions\Storage\Wiki\UploadedFileAction;
use App\Rules\Wiki\Submission\Audio\AudioCodecStreamRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AudioCodecStreamTest extends TestCase
{
    use WithFaker;

    /**
     * The Audio Codec Stream Rule shall fail if the audio connect is not opus.
     */
    public function testFailsWhenCodecIsNotOpus(): void
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
                        'codec_type' => 'audio',
                        'codec_name' => $this->faker->randomDigitNot(2),
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new AudioCodecStreamRule()],
        );

        static::assertFalse($validator->passes());

        Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
    }

    /**
     * The Audio Codec Stream Rule shall pass if the audio connect is opus.
     */
    public function testPassesWhenCodecIsOpus(): void
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
                        'codec_type' => 'audio',
                        'codec_name' => 'opus',
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new AudioCodecStreamRule()],
        );

        static::assertTrue($validator->passes());

        Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
    }
}
