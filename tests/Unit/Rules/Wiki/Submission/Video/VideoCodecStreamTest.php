<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Submission\Video;

use App\Actions\Storage\Wiki\UploadedFileAction;
use App\Constants\FeatureConstants;
use App\Rules\Wiki\Submission\Video\VideoCodecStreamRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class VideoCodecStreamTest extends TestCase
{
    use WithFaker;

    /**
     * The Video Codec Stream Rule shall fail if the video codec is not vp9.
     */
    public function testFailsWhenCodecIsNotVp9(): void
    {
        Feature::activate(FeatureConstants::VIDEO_CODEC_STREAM, 'vp9');

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
                        'codec_type' => 'video',
                        'codec_name' => $this->faker->randomDigitNot(2),
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new VideoCodecStreamRule()],
        );

        static::assertFalse($validator->passes());

        Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
    }

    /**
     * The Video Codec Stream Rule shall pass if the video codec is vp9.
     */
    public function testPassesWhenCodecIsVp9(): void
    {
        Feature::activate(FeatureConstants::VIDEO_CODEC_STREAM, 'vp9');

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
                        'codec_type' => 'video',
                        'codec_name' => 'vp9',
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new VideoCodecStreamRule()],
        );

        static::assertTrue($validator->passes());

        Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
    }
}
