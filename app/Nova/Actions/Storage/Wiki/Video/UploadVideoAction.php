<?php

declare(strict_types=1);

namespace App\Nova\Actions\Storage\Wiki\Video;

use App\Actions\Storage\Wiki\Video\UploadVideoAction as UploadVideo;
use App\Constants\Config\VideoConstants;
use App\Nova\Actions\Storage\Base\UploadAction;
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
use App\Rules\Wiki\Submission\Video\VideoCodecStreamRule;
use App\Rules\Wiki\Submission\Video\VideoColorPrimariesStreamRule;
use App\Rules\Wiki\Submission\Video\VideoColorSpaceStreamRule;
use App\Rules\Wiki\Submission\Video\VideoColorTransferStreamRule;
use App\Rules\Wiki\Submission\Video\VideoIndexStreamRule;
use App\Rules\Wiki\Submission\Video\VideoPixelFormatStreamRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rules\File as FileRule;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class UploadVideoAction.
 */
class UploadVideoAction extends UploadAction
{
    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.actions.video.upload.name');
    }

    /**
     * Get the underlying storage action.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return UploadVideo
     */
    protected function action(ActionFields $fields, Collection $models): UploadVideo
    {
        /** @var UploadedFile $file */
        $file = $fields->get('file');
        $path = $fields->get('path');

        return new UploadVideo($file, $path);
    }

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED);
    }

    /**
     * Get the file validation rules.
     *
     * @return array
     */
    protected function fileRules(): array
    {
        return [
            'required',
            FileRule::types('webm')->max(200 * 1024),
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
        ];
    }
}
