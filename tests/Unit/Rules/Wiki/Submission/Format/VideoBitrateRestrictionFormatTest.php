<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Submission\Format;

use App\Actions\Storage\Wiki\UploadedFileAction;
use App\Constants\FeatureConstants;
use App\Rules\Wiki\Submission\Format\VideoBitrateRestrictionFormatRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class VideoBitrateRestrictionFormatTest.
 */
class VideoBitrateRestrictionFormatTest extends TestCase
{
    use WithFaker;

    /**
     * The Video Bitrate Restriction Format Rule shall fail if the video bitrate is outside the accepted boundaries.
     *
     * @return void
     */
    public function testFailsWhenBitrateIsNotExpected(): void
    {
        Feature::activate(FeatureConstants::VIDEO_BITRATE_RESTRICTION);

        $height = $this->faker->numberBetween(360, 1080);
        $bitrate = $height * 14200 + 475000;

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
                    'bit_rate' => $bitrate,
                ],
                'streams' => [
                    0 => [
                        'codec_type' => 'video',
                        'height' => $height,
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new VideoBitrateRestrictionFormatRule()],
        );

        static::assertFalse($validator->passes());

        Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
    }

    /**
     * The Video Bitrate Restriction Format Rule shall fail if the video bitrate is within the accepted boundaries.
     *
     * @return void
     */
    public function testPassesWhenBitrateIsExpected(): void
    {
        Feature::activate(FeatureConstants::VIDEO_BITRATE_RESTRICTION);

        $height = $this->faker->numberBetween(360, 1080);
        $bitrate = $height * 3550 + 475000;

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
                    'bit_rate' => $bitrate,
                ],
                'streams' => [
                    0 => [
                        'codec_type' => 'video',
                        'height' => $height,
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => new VideoBitrateRestrictionFormatRule()],
        );

        static::assertTrue($validator->passes());

        Process::assertRan(UploadedFileAction::formatFfprobeCommand($file));
    }
}
