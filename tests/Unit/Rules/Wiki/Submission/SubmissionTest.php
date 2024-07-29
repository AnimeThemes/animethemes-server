<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Submission;

use App\Constants\FeatureConstants;
use App\Rules\Wiki\Submission\Audio\AudioChannelLayoutStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioChannelsStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioCodecStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioIndexStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioLoudnessIntegratedTargetStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioLoudnessTruePeakStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioSampleRateStreamRule;
use App\Rules\Wiki\Submission\Format\EncoderNameFormatRule;
use App\Rules\Wiki\Submission\Format\EncoderVersionFormatRule;
use App\Rules\Wiki\Submission\Format\ExtraneousChaptersFormatRule;
use App\Rules\Wiki\Submission\Format\ExtraneousMetadataFormatRule;
use App\Rules\Wiki\Submission\Format\FormatNameFormatRule;
use App\Rules\Wiki\Submission\Format\TotalStreamsFormatRule;
use App\Rules\Wiki\Submission\Format\VideoBitrateRestrictionFormatRule;
use App\Rules\Wiki\Submission\SubmissionRule;
use App\Rules\Wiki\Submission\Video\VideoCodecStreamRule;
use App\Rules\Wiki\Submission\Video\VideoColorPrimariesStreamRule;
use App\Rules\Wiki\Submission\Video\VideoColorSpaceStreamRule;
use App\Rules\Wiki\Submission\Video\VideoColorTransferStreamRule;
use App\Rules\Wiki\Submission\Video\VideoIndexStreamRule;
use App\Rules\Wiki\Submission\Video\VideoPixelFormatStreamRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class SubmissionTest.
 */
class SubmissionTest extends TestCase
{
    use WithFaker;

    /**
     * The Submission Rules shall execute FFmpeg processes once.
     *
     * @return void
     */
    public function testRunsProcessesOnce(): void
    {
        Feature::activate(FeatureConstants::REQUIRED_ENCODER_VERSION, 'Lavf59.27.100');
        Feature::activate(FeatureConstants::AUDIO_BITRATE_RESTRICTION);
        Feature::activate(FeatureConstants::VIDEO_BITRATE_RESTRICTION);
        Feature::activate(FeatureConstants::VIDEO_CODEC_STREAM, 'vp9');
        Feature::activate(FeatureConstants::VIDEO_COLOR_PRIMARIES_STREAM, 'bt709,smpte170m,bt470bg');
        Feature::activate(FeatureConstants::VIDEO_COLOR_SPACE_STREAM, 'bt709,smpte170m,bt470bg');
        Feature::activate(FeatureConstants::VIDEO_COLOR_TRANSFER_STREAM, 'bt709,smpte170m,bt470bg');
        Feature::activate(FeatureConstants::VIDEO_PIXEL_FORMAT_STREAM, 'yuv420p');

        $height = $this->faker->numberBetween(360, 1080);
        $bitrate = $height * 3550 + 475000;

        $file = UploadedFile::fake()->create($this->faker->word().'.webm', $this->faker->randomDigitNotNull());

        Process::fake([
            SubmissionRule::formatLoudnessCommand($file) => Process::result(errorOutput: json_encode([
                'input_i' => $this->faker->numberBetween(-17, -14),
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
                'chapters' => [],
                'format' => [
                    'bit_rate' => $bitrate,
                    'format_name' => 'matroska,webm',
                    'tags' => [
                        'ENCODER' => 'Lavf59.27.100',
                        'DURATION' => '00:01:30.098000000',
                    ],
                ],
                'streams' => [
                    0 => [
                        'codec_name' => 'vp9',
                        'codec_type' => 'video',
                        'color_primaries' => Arr::random(['bt709', 'smpte170m', 'bt470bg']),
                        'color_space' => Arr::random(['bt709', 'smpte170m', 'bt470bg']),
                        'color_transfer' => Arr::random(['bt709', 'smpte170m', 'bt470bg']),
                        'height' => $height,
                        'index' => 0,
                        'pix_fmt' => 'yuv420p',
                    ],
                    1 => [
                        'channel_layout' => 'stereo',
                        'channels' => 2,
                        'codec_name' => 'opus',
                        'codec_type' => 'audio',
                        'index' => 1,
                        'sample_rate' => '48000',
                    ],
                ],
            ])),
        ]);

        $validator = Validator::make(
            ['file' => [$file]],
            [
                'file' => [
                    new TotalStreamsFormatRule(2),
                    new EncoderNameFormatRule(),
                    new EncoderVersionFormatRule(),
                    new FormatNameFormatRule('matroska,webm'),
                    new VideoBitrateRestrictionFormatRule(),
                    new ExtraneousMetadataFormatRule(),
                    new ExtraneousChaptersFormatRule(),
                    new AudioIndexStreamRule(1),
                    new AudioCodecStreamRule(),
                    new AudioSampleRateStreamRule(),
                    new AudioChannelsStreamRule(),
                    new AudioChannelLayoutStreamRule(),
                    new AudioLoudnessTruePeakStreamRule(),
                    new AudioLoudnessIntegratedTargetStreamRule(),
                    new VideoIndexStreamRule(),
                    new VideoCodecStreamRule(),
                    new VideoPixelFormatStreamRule(),
                    new VideoColorSpaceStreamRule(),
                    new VideoColorTransferStreamRule(),
                    new VideoColorPrimariesStreamRule(),
                ],
            ],
        );

        static::assertTrue($validator->passes());

        Process::assertRanTimes(SubmissionRule::formatFfprobeCommand($file));
        Process::assertRanTimes(SubmissionRule::formatLoudnessCommand($file));
    }
}
